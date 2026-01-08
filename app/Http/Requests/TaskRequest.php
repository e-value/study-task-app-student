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
        // 403エラーを担当という認識だが合っているか？
        // （409はコントローラー側で記述）

        // 引数に応じて`project`の定義を分岐
        if ($this->project){
            $project = $this->project;
        }
        else if (!$this->project and $this->task){
            $project = $this->task->project;
        }
        else{
            return False;
        };

        // 自分が所属しているかチェック
        $isMember = $project->users()
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
        // 422エラーを担当
        
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:todo,doing,done',
        ];
    }
}
