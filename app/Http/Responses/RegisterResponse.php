<?php

namespace App\Http\Responses;

use App\Http\Responses\Concerns\RedirectsToCurrentGroup;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    use RedirectsToCurrentGroup;

    public function toResponse($request): Response
    {
        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 201)
            : redirect()->intended($this->redirectPathForCurrentGroup($request, Fortify::redirects('register')));
    }
}
