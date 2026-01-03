<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');
        
        if (!$project) {
            return false;
        }
        
        // 自分が所属しているかチェック（users()リレーションを使用）
        return $project->users()
            ->where('users.id', $this->user()->id)
            ->exists();
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        // JSONレスポンスを返す
        $response = response()->json([
                'message' => 'このプロジェクトにアクセスする権限がありません',
            ], 403);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:todo,doing,done',
        ];
    }

        /**
     * エラーメッセージ
     *
     * @return array
     */
    public function messages() {
        return response()->json([
            'message' => 'バリデーションエラー',
            'errors' => $validator->errors(),
        ], 422);
    }
}
