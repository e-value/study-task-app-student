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
        // プロジェクトのオーナーまたは管理者かチェック
        $project = $this->route('project');
        
        if (!$project) {
            return false;
        }

        $myUser = $project->users()
            ->where('users.id', $this->user()->id)
            ->first();

        if (!$myUser) {
            return false;
        }

        return in_array($myUser->pivot->role, ['project_owner', 'project_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:project_owner,project_admin,project_member',
        ];
    }
}

