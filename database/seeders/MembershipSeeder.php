<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();
        $member = User::where('email', 'member@example.com')->first();

        $project1 = Project::where('name', 'ECサイトリニューアルプロジェクト')->first();
        $project2 = Project::where('name', '新規モバイルアプリ開発')->first();

        // ECサイトプロジェクトのメンバーシップ（3人）
        Membership::create([
            'project_id' => $project1->id,
            'user_id' => $owner->id,
            'role' => 'project_owner',
        ]);

        Membership::create([
            'project_id' => $project1->id,
            'user_id' => $admin->id,
            'role' => 'project_admin',
        ]);

        Membership::create([
            'project_id' => $project1->id,
            'user_id' => $member->id,
            'role' => 'project_member',
        ]);

        // モバイルアプリプロジェクトのメンバーシップ（2人）
        Membership::create([
            'project_id' => $project2->id,
            'user_id' => $owner->id,
            'role' => 'project_owner',
        ]);

        Membership::create([
            'project_id' => $project2->id,
            'user_id' => $member->id,
            'role' => 'project_member',
        ]);
    }
}
