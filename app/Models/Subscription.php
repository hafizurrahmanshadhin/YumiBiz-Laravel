<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'package_type',
        'timeline',
        'price',
        'feature',
    ];

    protected $casts = [
        'feature' => 'array',
    ];

    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'rewinds_limit',
        'swipes_limit',
    ];

    public function memberships() {
        return $this->hasMany(Membership::class);
    }
}
