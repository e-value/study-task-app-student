<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\ConflictException;
use Throwable;

class ApiExceptionHandler
{
    private ApiResponse $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * API例外を処理する
     * 
     * @param Throwable $exception
     * @param Request $request
     * @return JsonResponse|null
     */
    public function handle(Throwable $exception, Request $request): ?JsonResponse
    {
        // API以外のリクエストは処理しない
        if (!$request->is('api/*')) {
            return null;
        }

        // 例外タイプに応じて処理を振り分け
        return match (true) {
            $exception instanceof NotFoundHttpException => $this->handleNotFound(),
            $exception instanceof ModelNotFoundException => $this->handleNotFound($exception->getMessage()),
            $exception instanceof ValidationException => $this->handleValidation($exception),
            $exception instanceof AuthenticationException => $this->handleAuthentication(),
            $exception instanceof AuthorizationException => $this->handleForbidden($exception),
            $exception instanceof ConflictException => $this->handleConflict($exception),
            default => $this->handleServerError($exception),
        };
    }

    /**
     * 404エラー
     */
    private function handleNotFound(?string $message = null): JsonResponse
    {
        return $this->response->notFound($message ?? '指定されたデータが見つかりません');
    }

    /**
     * 権限エラー（403）
     * Laravel標準のAuthorizationExceptionを処理
     */
    private function handleForbidden(AuthorizationException $exception): JsonResponse
    {
        return $this->response->forbidden($exception->getMessage());
    }

    /**
     * 競合エラー（409）
     * カスタム例外（Laravel標準にないため）
     */
    private function handleConflict(ConflictException $exception): JsonResponse
    {
        // 409 Conflictのレスポンスを返す
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 409);
    }

    /**
     * バリデーションエラー
     */
    private function handleValidation(ValidationException $exception): JsonResponse
    {
        return $this->response->validationError('バリデーションエラー', $exception->errors());
    }

    /**
     * 認証エラー
     */
    private function handleAuthentication(): JsonResponse
    {
        return $this->response->unauthorized();
    }

    /**
     * サーバーエラー（500）
     * どれにも当てはまらない場合はサーバーエラーとして処理
     */
    private function handleServerError(Throwable $exception): JsonResponse
    {
        // Sentryにエラーを送信
        app('sentry')->captureException($exception);

        // 本番では固定メッセージ、開発中は詳細表示
        $message = config('app.debug') ? $exception->getMessage() : 'サーバーエラー';

        return $this->response->serverError($message);
    }
}
