#!/bin/bash

# Study Task App セットアップスクリプト
# このスクリプトを実行すれば、プロジェクトを自動でセットアップできます

set -e

echo "🚀 Study Task App のセットアップを開始します..."
echo ""

# カラー定義
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Docker の確認
echo -e "${BLUE}[1/9]${NC} Docker Desktop の起動を確認中..."
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker Desktop が起動していません。${NC}"
    echo "Docker Desktop を起動してから、再度このスクリプトを実行してください。"
    exit 1
fi
echo -e "${GREEN}✅ Docker Desktop が起動しています${NC}"
echo ""

# 2. .env ファイルの作成
echo -e "${BLUE}[2/9]${NC} 環境変数ファイルを準備中..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ .env ファイルを作成しました${NC}"
else
    echo -e "${YELLOW}⚠️  .env ファイルは既に存在します（スキップ）${NC}"
fi
echo ""

# 3. Composer の依存関係をインストール
echo -e "${BLUE}[3/9]${NC} Composer パッケージをインストール中..."
if [ ! -d "vendor" ]; then
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
    echo -e "${GREEN}✅ Composer パッケージをインストールしました${NC}"
else
    echo -e "${YELLOW}⚠️  vendor ディレクトリは既に存在します（スキップ）${NC}"
fi
echo ""

# 4. コンテナを起動
echo -e "${BLUE}[4/9]${NC} Docker コンテナを起動中..."
./vendor/bin/sail up -d
echo -e "${GREEN}✅ コンテナを起動しました${NC}"
echo ""

# コンテナの起動を待つ
echo -e "${BLUE}[5/9]${NC} MySQL の準備ができるまで待機中..."
sleep 10
echo -e "${GREEN}✅ MySQL の準備が整いました${NC}"
echo ""

# 6. Node.js パッケージのインストール
echo -e "${BLUE}[6/9]${NC} Node.js パッケージをインストール中..."
./vendor/bin/sail npm install
echo -e "${GREEN}✅ Node.js パッケージをインストールしました${NC}"
echo ""

# 7. アプリケーションキーの生成
echo -e "${BLUE}[7/9]${NC} アプリケーションキーを生成中..."
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env || grep -q "APP_KEY=''" .env; then
    ./vendor/bin/sail artisan key:generate
    echo -e "${GREEN}✅ アプリケーションキーを生成しました${NC}"
else
    echo -e "${YELLOW}⚠️  APP_KEY は既に設定されています（スキップ）${NC}"
fi
echo ""

# 8. データベースマイグレーション＆シーダー実行
echo -e "${BLUE}[8/9]${NC} データベースをセットアップ中..."
./vendor/bin/sail artisan migrate:fresh --seed --force
echo -e "${GREEN}✅ データベースをセットアップしました${NC}"
echo ""

# 9. 完了メッセージ
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}🎉 セットアップが完了しました！${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${BLUE}次のステップ:${NC}"
echo ""
echo -e "1. 別のターミナルで以下のコマンドを実行してください:"
echo -e "   ${YELLOW}sail npm run dev${NC}"
echo ""
echo -e "   ※ ${YELLOW}sail${NC} コマンドが使えない場合は以下を実行："
echo -e "   ${YELLOW}./vendor/bin/sail npm run dev${NC}"
echo ""
echo -e "2. ブラウザで以下のURLを開いてください:"
echo -e "   ${YELLOW}http://localhost${NC}"
echo ""
echo -e "3. 以下のテストユーザーでログインできます:"
echo -e "   ${YELLOW}owner@example.com${NC} (password: ${YELLOW}password${NC})"
echo -e "   ${YELLOW}admin@example.com${NC} (password: ${YELLOW}password${NC})"
echo -e "   ${YELLOW}member@example.com${NC} (password: ${YELLOW}password${NC})"
echo ""
echo -e "${GREEN}Happy Coding! 🚀✨${NC}"

