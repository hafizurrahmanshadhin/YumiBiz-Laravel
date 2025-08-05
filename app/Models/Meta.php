<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meta extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
    ];

    public function userResponses(): HasMany {
        return $this->hasMany(UserResponse::class);
    }

    public function businessExperiences(): HasMany {
        return $this->hasMany(BusinessExperience::class);
    }
}
