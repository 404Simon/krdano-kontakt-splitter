<?php

namespace App\Services\Extractors;

use Closure;

class NameExtractor
{
    public function __invoke(array $data, Closure $next): array
    {
        $words = explode(' ', $data['remaining']);
        $wordsCount = count($words);

        if ($wordsCount === 0) {
            // No names found
            return $next($data);
        } elseif ($wordsCount === 1) {
            // assume its the last name
            $data['lastname'] = $words[0];
        } else {
            // Last word is the last name (Mueller-Meiser is one word), everything else is the first name
            $data['lastname'] = array_pop($words);
            $data['firstname'] = implode(' ', $words);
        }

        return $next($data);
    }
}
