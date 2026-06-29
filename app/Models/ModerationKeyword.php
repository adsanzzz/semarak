<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationKeyword extends Model
{
    protected $table = 'moderation_keywords';

    protected $fillable = [
        'keyword',
    ];
}
