<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'title', 'description', 'image'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }
}
