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
        // 自分が所属しているかチェック
        $isMember = $this->project->users()
            ->where('users.id', $this->user()->id)
            ->exists();
        
        return $isMember;
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
