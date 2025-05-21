<?php

namespace App\Services\Extractors;

use Closure;
use Illuminate\Support\Facades\Config;

class SalutationExtractor
{
    protected array $salutationMap;

    protected array $specialVariations;

    public function __construct()
    {
        $this->salutationMap = Config::get('languages.salutation');

        $this->specialVariations = Config::get('languages.specialSalutationVariations');
    }

    public function __invoke(array $data, Closure $next): array
    {
        $words = explode(' ', $data['remaining']);
        $firstWord = strtolower($words[0] ?? '');

        // Check if it's a special variation first
        if (isset($this->specialVariations[$firstWord])) {
            [$lang, $gender] = $this->specialVariations[$firstWord];

            // Get original case from input
            $originalWords = explode(' ', $data['remaining']);

            $data['salutation'] = $originalWords[0];
            $data['language'] = $lang;
            $data['gender'] = $gender;

            // Remove salutation from remaining text
            array_shift($words);
            $data['remaining'] = implode(' ', $words);

            return $next($data);
        }

        // Check standard variations for each language and gender
        foreach ($this->salutationMap as $lang => $genderSalutations) {
            foreach ($genderSalutations as $gender => $standardSalutation) {
                // Check if the first word matches the standard form (case insensitive)
                if (strtolower($standardSalutation) === $firstWord) {
                    // Get original case from input
                    $originalWords = explode(' ', $data['remaining']);

                    $data['salutation'] = $originalWords[0];
                    $data['language'] = $lang;
                    $data['gender'] = $gender;

                    // Remove salutation from remaining text
                    array_shift($words);
                    $data['remaining'] = implode(' ', $words);

                    return $next($data);
                }
            }
        }

        return $next($data);
    }
}
