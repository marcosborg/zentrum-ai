<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['user', 'messages'];
    protected $casts = [
        'messages' => 'array',
    ];
}
