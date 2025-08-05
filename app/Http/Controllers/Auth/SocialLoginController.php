<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller {
    public function googleRedirect() {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback() {
        $googleUser = Socialite::driver('google')->user();
        dd($googleUser);
    }

    public function googleLogin(Request $request) {
        $request->validate([
            'token' => 'required',
        ]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
            if ($googleUser) {
                $user      = User::where('email', $googleUser->email)->first();
                $isNewUser = false;
                if (!$user) {
                    //! Create a temporary user object without storing it in the database
                    $user = new User([
                        'name'           => $googleUser->name,
                        'email'          => $googleUser->email,
                        'password'       => bcrypt(Str::random(16)),
                        'agree_to_terms' => true,
                    ]);
                    $user->id  = 0;
                    $isNewUser = true;
                }
                $token = $user->createToken('auth_token')->plainTextToken;

                // Load relational data
                $data = $user->load(
                    'profile',
                    'userAddresses',
                    'photoGalleries',
                    'lookingFor',
                    'businessExperiences'
                );

                // Transform the lookingFor relationship data
                $lookingFor = $data->lookingFor->map(function ($item) {
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

                // Prepare the response data, removing the original looking_for field
                $responseData = [
                    'id'                   => $data->id,
                    'name'                 => $data->name,
                    'email'                => $data->email,
                    'looking_for'           => $lookingFor,
                    'profile'              => $data->profile,
                    'user_addresses'       => $data->userAddresses,
                    'photo_galleries'      => $data->photoGalleries,
                    'business_experiences' => $data->businessExperiences,
                ];

                return response()->json([
                    'status'    => true,
                    'message'   => $isNewUser ? 'User registered successfully' : 'User logged in successfully',
                    'token'     => $token,
                    'is_social' => 1,
                    'data'      => $responseData, // Use the cleaned response data
                ]);
            } else {
                return Helper::jsonResponse(false, 'Unauthorized', 401);
            }
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
