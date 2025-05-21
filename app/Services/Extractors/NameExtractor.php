<?php

namespace App\Services\Extractors;

use Closure;
use Illuminate\Support\Str;

class NameExtractor
{
    public function __invoke(array $data, Closure $next): array
    {
        $words = Str::of($data['remaining'])->explode(' ')->filter();

        if ($words->isEmpty()) {
            return $next($data);
        }

        if ($words->count() === 1) {
            // assume its the last name
            $data['lastname'] = $words->first();

            return $next($data);
        }

        // Check for prefixes
        $prefixIndex = $words->search(fn ($word) => in_array(Str::lower($word), config('languages.lastname_prefixes')));

        if ($prefixIndex !== false && $prefixIndex > 0) {
            // Split at prefix
            $data['firstname'] = $words->take($prefixIndex)->join(' ');
            $data['lastname'] = $words->skip($prefixIndex)->join(' ');
        } else {
            // Default behavior - last word is lastname
            $data['lastname'] = $words->last();
            $data['firstname'] = $words->slice(0, -1)->join(' ');
        }

        return $next($data);
    }
}
