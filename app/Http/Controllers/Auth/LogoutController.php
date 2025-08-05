<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller {
    /**
     * Log out the authenticated user.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function logout(): JsonResponse {
        try {
            auth()->logout();
            return Helper::jsonResponse(true, 'Logout successfully', 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
