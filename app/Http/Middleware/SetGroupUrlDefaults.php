<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetGroupUrlDefaults
{
    /**
     * Set the default URL parameters for group-based routes.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($currentGroup = $request->user()?->currentGroup) {
            URL::defaults([
                'current_group' => $currentGroup->slug,
                'group' => $currentGroup->slug,
            ]);
        }

        return $next($request);
    }
}
