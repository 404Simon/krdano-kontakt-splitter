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
        $words = explode(' ', $data['remaining']);
        $titles = [];
        $remainingWords = [];
        $titlePhase = true;

        foreach ($words as $word) {
            $wordLower = strtolower(rtrim($word, '.'));

            // Check if word is a title or part of a title pattern
            if ($titlePhase && (
                in_array($wordLower, $this->titles) ||
                // i think this is nice
                Str::endsWith($word, '.'))) {
                $titles[] = $word;
            } else {
                $titlePhase = false;
                $remainingWords[] = $word;
            }
        }

        $data['titles'] = $titles;
        $data['remaining'] = implode(' ', $remainingWords);

        return $next($data);
    }
}
