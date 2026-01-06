<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
        // 研修用に必要に応じてコメントアウト
        $response = new ApiResponse();

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                $requestId = $request->header('X-Request-ID') ?? uniqid('req_', true);
                return $response->notFound(requestId: $requestId);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                $requestId = $request->header('X-Request-ID') ?? uniqid('req_', true);
                return $response->validationError('バリデーションエラー', $e->errors(), $requestId);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                $requestId = $request->header('X-Request-ID') ?? uniqid('req_', true);
                return $response->unauthorized(requestId: $requestId);
            }
        });

        // どれにも当てはまらない場合はサーバーエラーとして処理
        // 注意：この処理は最後に配置すること
        $exceptions->render(function (Throwable $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                // 🎯 Sentryにコンテキスト情報を追加してエラーを送信
                $sentry = app('sentry');

                // リクエストIDを生成（クライアントとSentryで紐付け可能）
                $requestId = $request->header('X-Request-ID') ?? uniqid('req_', true);

                // Sentryにコンテキスト情報を設定
                $sentry->configureScope(function (\Sentry\State\Scope $scope) use ($request, $requestId, $e): void {
                    // 🎯 リクエスト情報をタグとして追加（Sentryで検索・フィルタリング可能）
                    $scope->setTag('request_id', $requestId);
                    $scope->setTag('http_method', $request->method());
                    $scope->setTag('route', $request->route()?->getName() ?? $request->path());
                    $scope->setTag('exception_type', get_class($e));

                    // リクエストIDを追加情報として記録（検索用）
                    $scope->setExtra('request_id', $requestId);

                    // ユーザー情報を追加（認証済みの場合）
                    if ($user = $request->user()) {
                        $scope->setUser([
                            'id' => $user->id,
                            'email' => $user->email,
                        ]);
                    }

                    // リクエスト詳細をコンテキストとして追加
                    $scope->setContext('request', [
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'request_id' => $requestId,
                    ]);

                    // リクエストパラメータを追加（機密情報は除外）
                    $params = $request->except(['password', 'password_confirmation', 'token']);
                    $scope->setContext('request_data', $params);
                });

                // エラーをSentryに送信
                // 🔍 エラーID（リクエストID）で検索する方法:
                // 1. シンプル検索: req_xxxxx （エラーメッセージに含まれるため検索可能）
                // 2. タグ検索: tags[request_id]:req_xxxxx
                // 3. 部分検索も可能

                // エラーメッセージにリクエストIDを含める（シンプル検索を可能にするため）
                $enhancedException = new \Exception(
                    $e->getMessage() . ' [ErrorID: ' . $requestId . ']',
                    $e->getCode(),
                    $e
                );
                $sentry->captureException($enhancedException);

                // 本番では固定メッセージ、開発中は詳細表示
                $message = config('app.debug') ? $e->getMessage() : 'サーバーエラー';

                // ✅ クライアントには固定メッセージ + エラーID（リクエストID）を返す
                // エラーIDがあれば、ユーザーが報告する際に「req_xxxxx」を伝えてもらえる
                // そのエラーIDでSentryを検索すると、該当エラーの詳細を確認できる
                return $response->serverError($message, $requestId);
            }
        });
    })->create();
