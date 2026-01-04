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
     *     }
     * }
     */
    private function respond(bool $success, string $message, $data, int $status, $errors = null): JsonResponse
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
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->badRequest('リクエストが不正です');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "リクエストが不正です"
     * }
     */
    public function badRequest(string $message = 'リクエストが不正です'): JsonResponse
    {
        return $this->respond(false, $message, null, 400);
    }

    /**
     * 認証エラー
     *
     * @param string $message
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->unauthorized('認証が必要です');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "認証が必要です"
     * }
     */
    public function unauthorized(string $message = '認証が必要です'): JsonResponse
    {
        return $this->respond(false, $message, null, 401);
    }

    /**
     * 権限エラー
     *
     * @param string $message
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->forbidden('権限がありません');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "権限がありません"
     * }
     */
    public function forbidden(string $message = '権限がありません'): JsonResponse
    {
        return $this->respond(false, $message, null, 403);
    }

    /**
     * 未検出エラー
     *
     * @param string $message
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->notFound('ユーザーが見つかりません');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "ユーザーが見つかりません"
     * }
     */
    public function notFound(string $message = 'リソースが見つかりません'): JsonResponse
    {
        return $this->respond(false, $message, null, 404);
    }

    /**
     * バリデーションエラー
     *
     * @param string $message
     * @param mixed $errors
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
     *     }
     * }
     */
    public function validationError(string $message = 'バリデーションエラー', $errors = null): JsonResponse
    {
        return $this->respond(false, $message, null, 422, $errors);
    }

    /**
     * サーバーエラー
     *
     * @param string $message
     * @return JsonResponse
     *
     * @example 使用例
     * return $this->response->serverError('サーバーエラーが発生しました');
     *
     * @example レスポンス例
     * {
     *     "success": false,
     *     "message": "サーバーエラーが発生しました"
     * }
     */
    public function serverError(string $message = 'サーバーエラー'): JsonResponse
    {
        return $this->respond(false, $message, null, 500);
    }
}
