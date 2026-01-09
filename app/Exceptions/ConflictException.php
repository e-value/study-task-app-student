<?php

namespace App\Exceptions;

use Exception;

/**
 * 競合エラー（409 Conflict）
 * 
 * リクエストが現在の状態と競合する場合に使用します。
 * 例：既に存在するデータの重複登録、不正な状態遷移など
 * 
 * @example
 * throw new ConflictException('このユーザーは既にプロジェクトのメンバーです');
 */
class ConflictException extends Exception
{
    /**
     * コンストラクタ
     *
     * @param string $message エラーメッセージ
     * @param int $code エラーコード
     * @param \Throwable|null $previous 前の例外
     */
    public function __construct(string $message = '競合が発生しました', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
