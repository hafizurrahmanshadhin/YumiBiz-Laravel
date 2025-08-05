<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FirebaseToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FirebaseTokenController extends Controller {
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [

            'token'     => 'required|string',
            'device_id' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = FirebaseToken::where('device_id', $request->device_id);
        if ($user) {
            $user->delete();
        }
        try {

            $data            = new FirebaseToken();
            $data->token     = $request->token;
            $data->device_id = $request->device_id;
            $data->user_type = $request->user_type;
            $data->save();

            return response()->json([
                'status'  => true,
                'message' => 'Token saved successfully',
                'data'    => $data,
                'code'    => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'No records found',
                'code'    => 418,
                'data'    => [],
            ], 418);
        }
    }

    /**
     * Get Single Record
     * @param $token, $device_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(Request $request) {

        $validator = Validator::make($request->all(), [

            'device_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $device_id = $request->device_id;
        $data      = FirebaseToken::where('id', $device_id)->first();
        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'No records found',
                'code'    => 404,
                'data'    => [],
            ], 404);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Token fetched successfully',
            'data'    => $data,
            'code'    => 200,
        ], 200);
    }

    /**
     * Delete Token Single Record
     * @param $token, $device_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteToken(Request $request) {
        $validator = Validator::make($request->all(), [

            'device_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = FirebaseToken::where('device_id', $request->device_id);
        if ($user) {
            $user->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Token deleted successfully',
                'code'    => 200,
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No records found',
                'code'    => 404,
            ], 404);
        }
    }

}
