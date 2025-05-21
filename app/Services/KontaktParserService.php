<?php

namespace App\Services;

use GenderDetector\Gender;
use GenderDetector\GenderDetector;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class KontaktParserService
{
    protected array $salutationMap;

    protected string $defaultLanguage;

    protected array $specialVariations;

    protected array $titles = [
        'prof', 'professor', 'dr', 'doctor', 'dipl', 'diplom', 'ing',
        'doc', 'rer', 'nat', 'med', 'phil', 'h.c', 'msc', 'ma', 'ba', 'phd',
    ];

    public function __construct(private GenderDetector $genderDetector)
    {
        $this->salutationMap = Config::get('languages.salutation');

        $this->defaultLanguage = Config::get('languages.default_language', 'DE');

        $this->specialVariations = Config::get('languages.specialSalutationVariations');
    }

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
            'language' => $this->defaultLanguage,
            'titles' => [],
            'firstname' => null,
            'lastname' => null,
            'gender' => null,
        ];

        $processedData = app(Pipeline::class)
            ->send($data)
            ->through([
                fn ($data, $next) => $this->extractSalutation($data, $next),
                fn ($data, $next) => $this->extractTitles($data, $next),
                fn ($data, $next) => $this->extractNames($data, $next),
                fn ($data, $next) => $this->determineGender($data, $next),
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
     * Extract salutation and determine language
     */
    protected function extractSalutation(array $data, \Closure $next): array
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

    /**
     * Extract titles from the remaining text
     */
    protected function extractTitles(array $data, \Closure $next): array
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

    /**
     * Extract first name and last name from the remaining text
     */
    protected function extractNames(array $data, \Closure $next): array
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

    /**
     * Determine gender based on salutation or first name
     */
    protected function determineGender(array $data, \Closure $next): array
    {
        // If gender is already set from salutation, keep it
        if ($data['gender'] !== null) {
            return $next($data);
        }

        // If we have a first name, use gender detector
        if ($data['firstname']) {
            $detectedGender = $this->genderDetector->getGender($data['firstname']);
            if (in_array($detectedGender, [Gender::Female, Gender::MostlyFemale])) {
                $data['gender'] = 'female';
            } else {
                $data['gender'] = 'male';
            }
        } else {
            // Default to male if no first name
            $data['gender'] = 'male';
        }

        return $next($data);
    }

    /**
     * Fill in missing salutation based on language and gender
     */
    protected function fillMissingSalutation(array $data, \Closure $next): array
    {
        if ($data['salutation'] === null) {
            // Default to the standard salutation for the detected language and gender
            $language = $data['language'] ?? $this->defaultLanguage;
            $gender = $data['gender'] ?? 'male'; // Default to male if gender is not set

            // Ensure language exists in map, otherwise fall back to default
            if (! isset($this->salutationMap[$language])) {
                $language = $this->defaultLanguage;
            }

            // Use male salutation as fallback if female doesn't exist for this language
            if (! isset($this->salutationMap[$language][$gender])) {
                $gender = 'male';
            }

            $data['salutation'] = $this->salutationMap[$language][$gender];
        }

        return $next($data);
    }
}
