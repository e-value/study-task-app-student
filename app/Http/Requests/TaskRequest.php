<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // プロジェクトのメンバーかチェック
        $project = $this->route('project');
        $task = $this->route('task');

        // storeの場合はprojectから、updateの場合はtaskからprojectを取得
        if ($project) {
            $targetProject = $project;
        } elseif ($task) {
            $targetProject = $task->project;
        } else {
            return false;
        }

        return $targetProject->users()
            ->where('users.id', $this->user()->id)
            ->exists();
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
            'title' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => $isUpdate ? 'sometimes|in:todo,doing,done' : 'nullable',
        ];
    }
}
