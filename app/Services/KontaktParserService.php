<?php

namespace App\Services;

use App\Services\Extractors\GenderExtractor;
use App\Services\Extractors\NameExtractor;
use App\Services\Extractors\SalutationExtractor;
use App\Services\Extractors\TitleExtractor;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Pipeline;

class KontaktParserService
{
    /**
     * Main function to extract contact details from a string
     */
    public function extractDetails(string $input): array
    {
        $input = $this->validateAndNormalizeInput($input);

        $data = [
            'original' => $input,
            'remaining' => $input,
            'salutation' => null,
            'language' => Config::get('language.default_language'),
            'titles' => [],
            'firstname' => null,
            'lastname' => null,
            'gender' => null,
        ];

        $processedData = Pipeline::send($data)
            ->through([
                new SalutationExtractor,
                new TitleExtractor,
                new NameExtractor,
                new GenderExtractor,
                fn ($data, $next) => $this->fillMissingSalutation($data, $next),
            ])
            ->thenReturn();

        return [
            'salutation' => $processedData['salutation'],
            'title' => implode(' ', $processedData['titles']),
            'gender' => $processedData['gender'],
            'firstname' => $processedData['firstname'],
            'lastname' => $processedData['lastname'],
            'language' => $processedData['language'],
        ];
    }

    /**
     * Validate and normalize the input string
     */
    protected function validateAndNormalizeInput(string $input): string
    {
        if (empty(trim($input))) {
            throw new \InvalidArgumentException('Input string cannot be empty');
        }

        // Trim whitespace and normalize spaces
        return preg_replace('/\s+/', ' ', trim($input));
    }

    /**
     * Fill in missing salutation based on language and gender
     */
    protected function fillMissingSalutation(array $data, \Closure $next): array
    {
        if (! $data['salutation']) {
            $salutationMap = Config::get('languages.salutation');
            $defaultLanguage = Config::get('languages.default_language');

            // Default to the standard salutation for the detected language and gender
            $language = $data['language'] ?? $defaultLanguage;
            $gender = $data['gender'] ?? 'male'; // Default to male if gender is not set

            // Ensure language exists in map, otherwise fall back to default
            if (! isset($salutationMap[$language])) {
                $language = $defaultLanguage;
            }

            // Use male salutation as fallback if female doesn't exist for this language
            if (! isset($salutationMap[$language][$gender])) {
                $gender = 'male';
            }

            $data['salutation'] = $salutationMap[$language][$gender];
        }

        return $next($data);
    }
}
