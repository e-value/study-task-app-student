<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
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
            $exception instanceof ValidationException => $this->handleValidation($exception),
            $exception instanceof AuthenticationException => $this->handleAuthentication(),
            default => $this->handleServerError($exception),
        };
    }

    /**
     * 404エラー
     */
    private function handleNotFound(): JsonResponse
    {
        return $this->response->notFound();
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
