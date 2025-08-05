<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Boost;
use App\Models\UserBoost;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BoostController extends Controller {
    public function purchaseBoost(Request $request) {
        $request->validate([
            'boost_id' => 'required|exists:boosts,id',
        ]);

        $boost = Boost::findOrFail($request->boost_id);
        $user  = auth()->user();

        // Calculate new expires_at time
        $newExpiresAt = null;

        // Find the maximum expires_at from existing boosts
        $maxExpiresAt = $user->boosts()->max('expires_at');

        if ($maxExpiresAt !== null) {
            // Calculate new expires_at based on the maximum expires_at found
            $newExpiresAt = Carbon::parse($maxExpiresAt)->addMinutes($boost->duration);
        } else {
            // If no existing boosts, set newExpiresAt to current time + boost duration
            $newExpiresAt = now()->addMinutes($boost->duration);
        }

        // Check if there's an existing boost or create a new one
        $existingBoost = $user->boosts()->where('boost_id', $boost->id)->first();

        if ($existingBoost) {
            // Update existing boost record
            $existingBoost->update([
                'activated_at' => now(),
                'expires_at'   => $newExpiresAt,
            ]);
        } else {
            // Create new boost record
            UserBoost::create([
                'user_id'      => $user->id,
                'boost_id'     => $boost->id,
                'activated_at' => now(),
                'expires_at'   => $newExpiresAt,
            ]);
        }

        // Update user's boost status
        $user->is_boost = 1;
        $user->save();

        return Helper::jsonResponse(true, 'Boost purchased successfully.', 200);
    }
}
