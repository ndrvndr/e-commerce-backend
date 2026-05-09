<?php

namespace App\Http\Resources\Concerns;

trait HasR2Url
{
    protected function r2Url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return rtrim(env('AWS_URL'), '/').'/'.ltrim($path, '/');
    }
}
