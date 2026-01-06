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
                return $response->notFound();
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->validationError('バリデーションエラー', $e->errors());
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->unauthorized();
            }
        });

        // どれにも当てはまらない場合はサーバーエラーとして処理
        // 注意：この処理は最後に配置すること
        $exceptions->render(function (Throwable $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                app('sentry')->captureException($e); // ← ★これを追加
                // 本番では固定メッセージ、開発中は詳細表示
                $message = config('app.debug') ? $e->getMessage() : 'サーバーエラー';
                return $response->serverError($message);
            }
        });
    })->create();
