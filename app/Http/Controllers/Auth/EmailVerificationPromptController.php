<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? response()->json(['verified' => true])
            : response()->json([
                'verified' => false,
                'status' => session('status')
            ]);
    }
}
