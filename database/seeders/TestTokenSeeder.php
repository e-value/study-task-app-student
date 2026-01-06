<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class TestTokenSeeder extends Seeder
{
    // 固定トークン（テスト用）
    // 本番環境では絶対に使用しないでください！
    private const FIXED_TOKENS = [
        'owner' => 'postman-owner-test-token-abc123def456ghi789jkl012mno345pqr678stu901vwx234',
        'admin' => 'postman-admin-test-token-xyz789abc012def345ghi678jkl901mno234pqr567stu890',
        'member' => 'postman-member-test-token-qwe456rty789uio012asd345fgh678jkl901zxc234vbn567',
    ];

    /**
     * Run the database seeds.
     * 
     * Postmanテスト用の固定トークンを生成します。
     * 本番環境では使用しないでください。
     */
    public function run(): void
    {
        // テストユーザーを取得
        $owner = User::where('email', 'owner@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();
        $member = User::where('email', 'member@example.com')->first();

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('🔑 Postmanテスト用トークン（完全固定）');
        $this->command->info('========================================');
        $this->command->info('');

        if ($owner) {
            $token = $this->createFixedToken($owner, 'postman-test-token', self::FIXED_TOKENS['owner']);

            $this->command->info('👤 オーナー (owner@example.com):');
            $this->command->info($token);
            $this->command->info('');
        }

        // 管理者用トークン（オプション）
        if ($admin) {
            $adminToken = $this->createFixedToken($admin, 'postman-test-token-admin', self::FIXED_TOKENS['admin']);

            $this->command->info('👤 管理者 (admin@example.com):');
            $this->command->info($adminToken);
            $this->command->info('');
        }

        // メンバー用トークン（オプション）
        if ($member) {
            $memberToken = $this->createFixedToken($member, 'postman-test-token-member', self::FIXED_TOKENS['member']);

            $this->command->info('👤 メンバー (member@example.com):');
            $this->command->info($memberToken);
            $this->command->info('');
        }

        $this->command->info('========================================');
        $this->command->info('📝 Postmanの設定方法:');
        $this->command->info('1. 環境変数に「token」を作成');
        $this->command->info('2. 上記のトークンをコピーして設定（一度だけ！）');
        $this->command->info('3. Authorization > Bearer Token > {{token}}');
        $this->command->info('');
        $this->command->info('💡 このトークンは完全固定です');
        $this->command->info('   何度 migrate:fresh --seed しても同じトークンです');
        $this->command->info('========================================');
        $this->command->info('');
    }

    /**
     * 固定トークンを作成（既存があれば削除して再作成）
     */
    private function createFixedToken(User $user, string $tokenName, string $plainToken): string
    {
        // 既存の同名トークンを削除
        $user->tokens()->where('name', $tokenName)->delete();

        // 固定トークンをハッシュ化してDBに保存
        $hashedToken = hash('sha256', $plainToken);

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => $tokenName,
            'token' => $hashedToken,
            'abilities' => json_encode(['*']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // トークンID|plainToken の形式で返す
        // トークンIDを取得
        $tokenId = DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->where('name', $tokenName)
            ->value('id');

        return $tokenId . '|' . $plainToken;
    }
}
