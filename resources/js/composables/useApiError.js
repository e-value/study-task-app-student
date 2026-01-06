import { ref } from "vue";
import { extractErrorMessage, extractValidationErrors } from "@/utils/apiError";

/**
 * APIエラーハンドリング用のComposable
 *
 * エラーメッセージとバリデーションエラーの状態管理を提供します。
 *
 * 【基本方針】
 * バックエンドが環境に応じて適切なメッセージを返すため、
 * フロントエンドは基本的にそれを信頼して表示します。
 * デフォルトメッセージは「万が一」のフォールバックです。
 *
 * @returns {Object} エラーハンドリング用のオブジェクト
 *
 * @example
 * const { error, validationErrors, handleError, clearError } = useApiError();
 *
 * try {
 *   await axios.get('/api/tasks/1');
 * } catch (err) {
 *   // 第2引数はフォールバック。ほとんどの場合、バックエンドのメッセージが使われる
 *   handleError(err, 'タスクの読み込みに失敗しました');
 * }
 */
export function useApiError() {
    const error = ref(null);
    const validationErrors = ref({});

    /**
     * エラーを処理する
     *
     * @param {Error} err - Axiosエラーオブジェクト
     * @param {string} defaultMessage - デフォルトメッセージ
     */
    const handleError = (err, defaultMessage = "エラーが発生しました") => {
        console.error("API Error:", err);

        // エラーメッセージを抽出
        error.value = extractErrorMessage(err, defaultMessage);

        // バリデーションエラーを抽出
        const validation = extractValidationErrors(err);
        if (validation) {
            validationErrors.value = validation;
        } else {
            validationErrors.value = {};
        }
    };

    /**
     * エラーをクリアする
     */
    const clearError = () => {
        error.value = null;
        validationErrors.value = {};
    };

    /**
     * エラーメッセージのみを設定する（手動でエラーを設定したい場合）
     *
     * @param {string} message - エラーメッセージ
     */
    const setError = (message) => {
        error.value = message;
    };

    return {
        error,
        validationErrors,
        handleError,
        clearError,
        setError,
    };
}
