<?php

namespace App\Services\Extractors;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class TitleExtractor
{
    public function __invoke(array $data, Closure $next): array
    {
        $user = Auth::user();
        $titles = collect(Config::get('languages.defaultSupportedTitles'))
            ->merge($user ? $user->supportedTitles->pluck('title') : [])
            ->map(fn ($t) => (string) str($t)->lower()->rtrim('.'))
            ->unique();

        $words = collect(explode(' ', trim($data['remaining'])))
            ->filter()
            ->values();

        // Find first word that is NOT a title and doesn't end with a dot
        $cut = $words->search(fn ($word) => ! $titles->contains(strtolower(rtrim($word, '.')))
            && ! Str::endsWith($word, '.')
        );

        // If none found, consume all as titles
        $cut = $cut === false ? $words->count() : $cut;

        $data['titles'] = $words->take($cut)->values()->all();
        $data['remaining'] = $words->skip($cut)->implode(' ');

        return $next($data);
    }
}
