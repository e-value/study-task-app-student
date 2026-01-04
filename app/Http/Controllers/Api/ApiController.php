<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;

class ApiController extends Controller
{
    protected function response(): ApiResponse
    {
        // 研修用に必要に応じてコメントアウト
        return new ApiResponse();
    }
}
