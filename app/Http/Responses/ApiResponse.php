<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * 共通処理
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @param mixed $errors
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example レスポンス例（成功時）
     * {
     *     "success": true,
     *     "message": "成功",
     *     "data": {
     *         "id": 1,
     *         "name": "田中太郎"
     *     }
     * }
     *
     * @example レスポンス例（エラー時）
     * {
     *     "success": false,
     *     "message": "バリデーションエラー",
     *     "errors": {
     *         "email": ["メールアドレスは必須です"]
     *     },
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    private function respond(bool $success, string $message, $data, int $status, $errors = null, ?string $requestId = null): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        // リクエストIDがある場合は追加（エラー時など）
        if ($requestId !== null) {
            $response['request_id'] = $requestId;
        }

        return response()->json($response, $status);
    }

    /**
     * 成功レスポンス
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->success($user, 'ユーザーを取得しました');
     *
     * @example レスポンス例
     * {
     *     "success": true,
     *     "message": "ユーザーを取得しました",
     *     "data": {"id": 1, "name": "田中太郎", "email": "tanaka@example.com"}
     * }
     */
    public function success($data = null, string $message = '成功'): JsonResponse
    {
        return $this->respond(true, $message, $data, 200);
    }

    /**
     * 作成成功レスポンス
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->created($user, 'ユーザーを作成しました');
     *
     * @example レスポンス例
     * {
     *     "success": true,
     *     "message": "ユーザーを作成しました",
     *     "data": {"id": 1, "name": "田中太郎", "email": "tanaka@example.com"}
     * }
     */
    public function created($data = null, string $message = '作成しました'): JsonResponse
    {
        return $this->respond(true, $message, $data, 201);
    }

    /**
     * 不正リクエストエラー
     *
     * @param string $message
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->badRequest('リクエストが不正です');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "リクエストが不正です",
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    public function badRequest(string $message = 'リクエストが不正です', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 400, null, $requestId);
    }

    /**
     * 認証エラー
     *
     * @param string $message
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->unauthorized('認証が必要です');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "認証が必要です",
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    public function unauthorized(string $message = '認証が必要です', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 401, null, $requestId);
    }

    /**
     * 権限エラー
     *
     * @param string $message
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->forbidden('権限がありません');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "権限がありません",
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    public function forbidden(string $message = '権限がありません', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 403, null, $requestId);
    }

    /**
     * 未検出エラー
     *
     * @param string $message
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->notFound('ユーザーが見つかりません');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "ユーザーが見つかりません",
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    public function notFound(string $message = '指定されたデータが見つかりません', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 404, null, $requestId);
    }

    /**
     * バリデーションエラー
     *
     * @param string $message
     * @param mixed $errors
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->validationError('バリデーションエラー', $e->errors());
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "バリデーションエラー",
     *     "errors": {
     *         "email": ["メールアドレスは必須です"],
     *         "name": ["名前は255文字以内で入力してください"]
     *     },
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    public function validationError(string $message = 'バリデーションエラー', $errors = null, ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 422, $errors, $requestId);
    }

    /**
     * サーバーエラー
     *
     * @param string $message
     * @param string|null $requestId
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->serverError('サーバーエラーが発生しました');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "サーバーエラーが発生しました",
     *     "request_id": "req_67890abcdef12345"
     * }
     */
    public function serverError(string $message = 'サーバーエラー', ?string $requestId = null): JsonResponse
    {
        return $this->respond(false, $message, null, 500, null, $requestId);
    }
}
