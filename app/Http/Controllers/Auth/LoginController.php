<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller {
    /**
     * Log in an existing user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse {
        try {
            $credentials = $request->only('email', 'password');
            $token       = auth()->attempt($credentials);

            if (!$token) {
                return Helper::jsonResponse(false, 'Unauthorized', 401);
            }

            $user = auth()->user()->load(
                'profile',
                'userAddresses',
                'userEducations',
                'photoGalleries',
                'lookingFor',
                'businessExperiences'
            );

            //! Transform the lookingFor relationship data
            $lookingFor = $user->lookingFor->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'type'        => 'lookingFor',
                    'description' => $item->description,
                    'pivot'       => [
                        'user_id' => (string) $item->pivot->user_id,
                        'meta_id' => (string) $item->pivot->meta_id,
                    ],
                ];
            });

            $data                = $user->toArray();
            $data['looking_for'] = $lookingFor;

            return response()->json([
                'status'     => true,
                'message'    => 'User logged in successfully.',
                'code'       => 200,
                'token_type' => 'bearer',
                'token'      => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
                'data'       => $data,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
