<?php

namespace KaanTanis\FilamentModelTranslatable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTranslatable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];
}
