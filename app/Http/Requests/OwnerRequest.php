<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRequest extends ProjectRequest
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
        
        // 自分がオーナーまたは管理者かチェック
        $myUser = $project->users()
            ->where('users.id', $this->user()->id)
            ->first();
        
        return $myUser && in_array($myUser->pivot->role, ['project_owner']);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        // JSONレスポンスを返す
        $response = response()->json([
                'message' => 'オーナーの権限がないため、エラー',
            ], 403);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return parent::rules();
    }
}
