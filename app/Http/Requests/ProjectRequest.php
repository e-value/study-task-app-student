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
        // show, update, destroyの場合
        if ($this->project){
            // 自分が所属しているかチェック
            $isMember = $this->project->users()
                ->where('users.id', $this->user()->id)
                ->exists();
            return $isMember;
        }
        // index, storeの場合
        else{
            // ログインユーザーであればプロジェクトを作成できる
            return $this->user() !== null;
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'is_archived' => 'sometimes|boolean',
        ];
    }
}
