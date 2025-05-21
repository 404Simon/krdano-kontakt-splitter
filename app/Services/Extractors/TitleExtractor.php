<?php

namespace App\Services\Extractors;

use Closure;
use Illuminate\Support\Str;

class TitleExtractor
{
    protected array $titles = [
        'prof', 'professor', 'dr', 'doctor', 'dipl', 'diplom', 'ing',
        'doc', 'rer', 'nat', 'med', 'phil', 'h.c', 'msc', 'ma', 'ba', 'phd',
    ];

    public function __invoke(array $data, Closure $next): array
    {
        $words = collect(explode(' ', trim($data['remaining'])))
            ->filter(fn ($word) => ! empty($word));

        $titleEndIndex = $words->search(function ($word) {
            $normalized = strtolower(rtrim($word, '.'));

            return ! in_array($normalized, $this->titles) && ! Str::endsWith($word, '.');
        });

        if ($titleEndIndex !== false) {
            $data['titles'] = $words->take($titleEndIndex)->values()->all();
            $data['remaining'] = $words->skip($titleEndIndex)->implode(' ');
        }

        return $next($data);
    }
}
