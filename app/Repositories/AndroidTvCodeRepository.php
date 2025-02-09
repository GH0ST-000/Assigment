<?php

namespace App\Repositories;

namespace App\Repositories;

use App\Models\AndroidTvCode;
use Carbon\Carbon;

/**
 * Class AndroidTvCodeRepository
 *
 * This class represents a repository for managing Android TV codes.
 */
class AndroidTvCodeRepository
{
    public function create(int $userId, string $code, Carbon $expiresAt): AndroidTvCode
    {
        return AndroidTvCode::create([
            'user_id' => $userId,
            'one_time_code' => $code,
            'expires_at' => $expiresAt,
        ]);
    }

    public function getValidCode(string $code): ?AndroidTvCode
    {
        return AndroidTvCode::where('one_time_code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    public function getCode(string $code): ?AndroidTvCode
    {
        return AndroidTvCode::where('one_time_code', $code)->first();
    }
}
