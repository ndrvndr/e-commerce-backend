<?php

if (! function_exists('r2_url')) {
    function r2_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return rtrim(env('AWS_URL'), '/').'/'.ltrim($path, '/');
    }
}
