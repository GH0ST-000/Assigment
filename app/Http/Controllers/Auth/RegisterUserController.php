<?php

namespace App\Http\Controllers\Auth;

use App\enums\Dictionary;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class RegisterUserController
 *
 * This class handles the registration of a new user.
 */
class RegisterUserController extends Controller
{
    public function __invoke(UserRegister $request): \Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        $user = $this->createUser($request);
        $token = $this->createUserToken($user);

        return responder()->success(['user' => $user, 'token' => $token]);
    }

    private function createUser(UserRegister $request): User
    {
        return app(User::class)::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    private function createUserToken(User $user): string
    {
        return $user->createToken(Dictionary::TOKEN)->accessToken;
    }
}
