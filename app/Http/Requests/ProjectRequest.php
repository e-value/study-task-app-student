<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // store（POST）の場合はログインユーザーであれば作成できる
        if ($this->isMethod('POST')) {
            return $this->user() !== null;
        }

        // update（PUT/PATCH）の場合はオーナーまたは管理者かチェック
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
        // store（POST）の場合は必須、update（PUT/PATCH）の場合はsometimes
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        return [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'is_archived' => $isUpdate ? 'sometimes|boolean' : 'boolean',
        ];
    }
}
