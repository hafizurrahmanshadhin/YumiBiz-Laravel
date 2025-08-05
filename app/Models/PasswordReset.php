<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordReset extends Model {
    use HasFactory;

    protected $table    = 'password_resets';
    protected $fillable = ['email', 'otp', 'created_at'];
    public $timestamps  = false;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
