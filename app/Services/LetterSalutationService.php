<?php

namespace App\Services;

use Illuminate\Support\Arr;

class LetterSalutationService
{
    /*
    * Generates the letter salutation based on the structured data
    */
    public static function generate(array $structured): string
    {
        $greetings = config('languages.greetings');
        $defaultLang = config('languages.default_language');
        $lang = Arr::get($structured, 'language', $defaultLang);
        $prefixes = $greetings[$lang] ?? $greetings[$defaultLang];

        $prefix = match (Arr::get($structured, 'gender')) {
            'male' => $prefixes[0],
            'female' => $prefixes[1],
            default => $prefixes[2],
        };

        return collect([
            $prefix,
            Arr::get($structured, 'salutation'),
            Arr::get($structured, 'title'),
            Arr::get($structured, 'lastname'),
        ])->filter()->join(' ');
    }
}
