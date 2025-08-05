<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model {
    use HasFactory;
    protected $fillable = [
        'user_id',
        'profile_id',
        'is_like',
        'status',
    ];

    protected $casts = [
        'user_id'    => 'string',
        'profile_id' => 'string',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profile(): BelongsTo {
        return $this->belongsTo(Profile::class);
    }
}
