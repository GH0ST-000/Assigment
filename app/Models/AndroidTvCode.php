<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AndroidTvCode extends Model
{
    protected $fillable = ['user_id', 'one_time_code', 'expires_at'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
