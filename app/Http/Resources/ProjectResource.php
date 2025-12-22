<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * TODO: Lesson1 で実装してください
     * 
     * 考えるべきポイント：
     * - どのような情報をフロントエンドに返すべきか？
     * - DBのカラムをそのまま返すべきか？加工すべきか？
     * - 関連データ（メンバー、タスクなど）をどう扱うべきか？
     * - 一覧表示用と詳細表示用で返す情報を分けるべきか？
     * 
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // TODO: プロジェクト情報のJSON構造を設計してください
        return [];
    }
}
