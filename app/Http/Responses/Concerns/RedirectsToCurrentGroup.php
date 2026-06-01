<?php

namespace App\Http\Responses\Concerns;

use Illuminate\Support\Facades\URL;

trait RedirectsToCurrentGroup
{
    protected function redirectPathForCurrentGroup($request, string $redirect): string
    {
        $group = $this->currentGroup($request);

        URL::defaults(['current_group' => $group->slug]);

        return "/{$group->slug}{$redirect}";
    }

    protected function currentGroup($request)
    {
        $user = $request->user();
        $group = $user?->currentGroup ?? $user?->personalGroup();

        if (! $group) {
            abort(403);
        }

        return $group;
    }
}
