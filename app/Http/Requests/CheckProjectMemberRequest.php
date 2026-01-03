<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckProjectMemberRequest extends OwnerAdminRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }
        
        $project = $this->route('project');
        
        if (!$project) {
            return false;
        }

        $userId = $this->input('user_id');
        
        if (!$userId) {
            return false;
        }

        // 既にメンバーかチェック（users()リレーションを使用）
        $existingUser = $project->users()
            ->where('users.id', $userId)
            ->exists();
        
        return !$existingUser;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'sometimes|string|in:project_owner,project_admin,project_member',
        ];
    }

    protected function failedAuthorization()
    {
        // JSONレスポンスを返す
        $response = response()->json([
                'message' => 'このユーザーは既にプロジェクトのメンバーです',
            ], 409);
    }

}
