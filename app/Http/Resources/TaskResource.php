<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * TODO: Lesson1 で実装してください
     * 
     * 考えるべきポイント：
     * - どのような情報をフロントエンドに返すべきか？
     * - DBのカラムをそのまま返すべきか？加工すべきか？
     * - 関連データ（作成者、プロジェクトなど）をどう扱うべきか？
     * - ステータス（todo/doing/done）をどう表現すべきか？
     * - 一覧表示用と詳細表示用で返す情報を分けるべきか？
     * 
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // TODO: タスク情報のJSON構造を設計してください
        return [];
    }
}
