<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model {
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'user_id' => 'string',
        'age'     => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
    ];

    // Define gender constants
    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    // Add the possible values as a static array
    public static $genders = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }
}
