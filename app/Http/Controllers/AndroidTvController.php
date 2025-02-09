<?php

namespace App\Http\Controllers;

use App\Services\AndroidTvCodeServices;
use Illuminate\Http\Request;

/**
 * Class AndroidTvController
 *
 * This class represents a controller for managing Android TV codes.
 */
class AndroidTvController extends Controller
{
    protected AndroidTvCodeServices $androidTvCodeServices;

    public function __construct(AndroidTvCodeServices $codeService)
    {
        $this->androidTvCodeServices = $codeService;
    }

    public function generateTvCode(Request $request): \Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        return $this->androidTvCodeServices->generateTvCode($request);
    }

    public function activeTvCode(Request $request): \Flugg\Responder\Http\Responses\ErrorResponseBuilder|\Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        $request->validate(['code' => 'required|string']);

        return $this->androidTvCodeServices->activeTvCode($request);
    }

    public function pollTvCode(Request $request): \Flugg\Responder\Http\Responses\ErrorResponseBuilder|\Flugg\Responder\Http\Responses\SuccessResponseBuilder
    {
        $request->validate(['code' => 'required|string']);

        return $this->androidTvCodeServices->pollTvCode($request);
    }
}
