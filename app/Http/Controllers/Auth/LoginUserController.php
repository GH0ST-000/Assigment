<?php

namespace App\Http\Controllers\Auth;

use App\enums\Dictionary;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLogin;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LoginUserController
 *
 * This class handles user login functionality by authenticating user credentials,
 * generating a token for the user, and returning appropriate responses based on the
 * authentication result.
 */
class LoginUserController extends Controller
{
    public function __invoke(UserLogin $request): \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Flugg\Responder\Http\Responses\ErrorResponseBuilder
    {
        if (! $this->authenticate($request)) {
            return responder()->error(Response::HTTP_UNAUTHORIZED, 'Invalid credentials');
        }

        $user = Auth::user();
        $token = $this->createUserToken($user);

        return responder()->success(['user' => $user, 'token' => $token]);
    }

    private function authenticate(UserLogin $request): bool
    {
        return Auth::attempt(['email' => $request->email, 'password' => $request->password]);
    }

    private function createUserToken($user): string
    {
        return $user->createToken(Dictionary::TOKEN)->accessToken;
    }
}
