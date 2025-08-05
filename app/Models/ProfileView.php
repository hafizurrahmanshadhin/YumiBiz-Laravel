<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model {
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'viewer_id'  => 'string',
        'profile_id' => 'string',
    ];

    public function viewer() {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    public function profile() {
        return $this->belongsTo(User::class, 'profile_id');
    }
}
