<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // プロジェクト1
        Project::create([
            'name' => 'ECサイトリニューアルプロジェクト',
            'is_archived' => false,
        ]);

        // プロジェクト2
        Project::create([
            'name' => '新規モバイルアプリ開発',
            'is_archived' => false,
        ]);

        // アーカイブ済みプロジェクト（参考用）
        Project::create([
            'name' => '旧システム保守',
            'is_archived' => true,
        ]);
    }
}


