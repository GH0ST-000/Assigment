<?php

namespace App\Services;

use App\Repositories\AndroidTvCodeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AndroidTvCodeServices
 */
class AndroidTvCodeServices
{
    private AndroidTvCodeRepository $androidTvCodeRepository;

    public function __construct(AndroidTvCodeRepository $repository)
    {
        $this->androidTvCodeRepository = $repository;
    }

    public function generateTvCode(Request $request): \Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        $user = $request->user();
        $codeData = $this->saveTVCodeForUser($user);

        return responder()->success($codeData);
    }

    public function activeTvCode(Request $request): \Flugg\Responder\Http\Responses\ErrorResponseBuilder|\Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        $code = $this->androidTvCodeRepository->getValidCode($request->code);
        if (! $code) {
            return responder()->error(Response::HTTP_BAD_REQUEST, 'Invalid or expired code');
        }

        $token = $this->activateTVCodeForUser($code);

        return responder()->success(['access_token' => $token]);
    }

    public function pollTvCode(Request $request): \Flugg\Responder\Http\Responses\ErrorResponseBuilder|\Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        $code = $this->androidTvCodeRepository->getCode($request->code);
        if (! $code || $code->expires_at < Carbon::now()) {
            return responder()->error(Response::HTTP_BAD_REQUEST, 'Code invalid or expired');
        }

        return responder()->success(['message' => 'Code valid, waiting for activation']);
    }

    private function saveTVCodeForUser($user): array
    {
        $code = $this->generateUniqueCode();
        $expiresAt = Carbon::now()->addMinutes(5);
        $this->androidTvCodeRepository->create($user->id, $code, $expiresAt);

        return ['one_time_code' => $code, 'expires_at' => $expiresAt];
    }

    private function activateTVCodeForUser($code)
    {
        $user = $code->user;
        if (! $user) {
            return responder()->error(Response::HTTP_BAD_REQUEST, 'No user associated with the code');
        }

        $token = $user->createToken('Android TV Token')->accessToken;
        $code->delete();

        return $token;
    }

    private function generateUniqueCode(): string
    {
        return bin2hex(random_bytes(3));
    }
}
