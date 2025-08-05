<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\OTPVerificationRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Mail\OTPMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller {
    /**
     * Send OTP code to the user's email.
     *
     * @param OTPRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function SendOTPCode(OTPRequest $request): JsonResponse {
        try {
            $email = $request->input('email');
            $otp   = rand(1000, 9999);
            $user  = User::where('email', $email)->first();

            if ($user) {
                //! OTP Email Address
                Mail::to($email)->send(new OTPMail($otp));
                //! Update OTP in password_resets table
                PasswordReset::updateOrCreate(
                    [
                        'email' => $email,
                    ],
                    [
                        'otp'        => $otp,
                        'created_at' => Carbon::now(),
                    ]
                );
                return Helper::jsonResponse(true, 'OTP Code Sent Successfully', 200);
            } else {
                return Helper::jsonResponse(false, 'Invalid Email Address', 401);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify the provided OTP code.
     *
     * @param OTPVerificationRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function VerifyOTP(OTPVerificationRequest $request): JsonResponse {
        try {
            $email = $request->header('email');
            $otp   = $request->input('otp');

            $passwordReset = PasswordReset::where('email', $email)
                ->where('otp', $otp)
                ->where('created_at', '>=', Carbon::now()->subMinutes(15))
                ->first();

            if ($passwordReset) {
                $passwordReset->delete();
                return Helper::jsonResponse(true, 'OTP Verified Successfully', 200);
            } else {
                return Helper::jsonResponse(false, 'Invalid OTP Code', 401);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Reset the user's password.
     *
     * @param PasswordResetRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function ResetPassword(PasswordResetRequest $request): JsonResponse {
        try {
            $email    = $request->header('email');
            $password = Hash::make($request->input('password'));

            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'password' => $password,
                ]);
                return Helper::jsonResponse(true, 'Password Reset Successfully', 200);
            } else {
                return Helper::jsonResponse(false, 'Invalid Email Address', 401);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
