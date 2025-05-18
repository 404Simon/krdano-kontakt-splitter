<?php

namespace App\Services;

use Exception;
use GenderDetector\Gender;
use GenderDetector\GenderDetector;
use Illuminate\Support\Facades\Log;
use TheIconic\NameParser\Language\English;
use TheIconic\NameParser\Language\German;
use TheIconic\NameParser\Parser;

class KontaktParserService
{
    public function extractDetails(string $input): ?array
    {
        try {
            $parser = new Parser([
                new German, // default
                new English,
            ]);

            $name = $parser->parse($input);

            $parsedSalutation = $name->getSalutation();
            $firstname = $name->getFirstname();
            if ($name->getMiddlename()) {
                $firstname = $firstname.' '.$name->getMiddlename();
            }
            $lastname = $name->getLastname(); // Includes prefixes like von, de etc.

            $estimatedGender = null;
            $estimatedLanguage = null;
            $derivedSalutation = null;

            $lowerParsedSalutation = strtolower($parsedSalutation);

            // Heuristic: Prioritize gender from the parsed salutation
            if (in_array($lowerParsedSalutation, ['herr', 'mr', 'señor', 'sr'])) {
                $estimatedGender = 'male';
            } elseif (in_array($lowerParsedSalutation, ['frau', 'mrs', 'ms', 'señora', 'sra'])) {
                $estimatedGender = 'female';
            }

            // Heuristic: Initial language estimation based *only* on parsed salutation
            if (in_array($lowerParsedSalutation, ['herr', 'frau'])) {
                $estimatedLanguage = 'DE';
            } elseif (in_array($lowerParsedSalutation, ['mr.', 'mrs.', 'ms.'])) {
                $estimatedLanguage = 'EN';
            } elseif (in_array($lowerParsedSalutation, ['señor', 'señora', 'sr.', 'sra.'])) {
                $estimatedLanguage = 'ES'; // Spanish example
            }

            $detectedGender = new GenderDetector()->getGender($firstname);

            $estimatedGender = "male";
            if($detectedGender && in_array($detectedGender, [Gender::Female, Gender::MostlyFemale]))
            {
                $estimatedGender = "female";
            }

            // Heuristic: Derive a salutation if the parser didn't find one, but we estimated gender
            if (empty($parsedSalutation) && $estimatedGender !== null) {
                $langForDerivedSalutation = $estimatedLanguage ?? 'DE'; // Default to DE

                if ($estimatedGender === 'male') {
                    if ($langForDerivedSalutation === 'EN') {
                        $derivedSalutation = 'Mr.';
                    } elseif ($langForDerivedSalutation === 'ES') {
                        $derivedSalutation = 'Sr.';
                    } else {
                        $derivedSalutation = 'Herr';
                    } // Default German
                } elseif ($estimatedGender === 'female') {
                    if ($langForDerivedSalutation === 'EN') {
                        $derivedSalutation = 'Ms.';
                    } elseif ($langForDerivedSalutation === 'ES') {
                        $derivedSalutation = 'Sra.';
                    } else {
                        $derivedSalutation = 'Frau';
                    } // Default German
                }

                // if salutation is derived, ensure language field reflects it
                if (! empty($derivedSalutation)) {
                    if (in_array($derivedSalutation, ['Herr', 'Frau'])) {
                        $estimatedLanguage = 'DE';
                    } elseif (in_array($derivedSalutation, ['Mr.', 'Ms.'])) {
                        $estimatedLanguage = 'EN';
                    } elseif (in_array($derivedSalutation, ['Sr.', 'Sra.'])) {
                        $estimatedLanguage = 'ES';
                    }
                }
            }

            $structuredResponse = [
                'salutation' => ! empty($parsedSalutation) ? $parsedSalutation : $derivedSalutation,
                'title' => null, // The parser includes titles in Salutation, doesn't separate them.
                'gender' => $estimatedGender,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'language' => $estimatedLanguage,
            ];
            return $structuredResponse;

        } catch (\Exception $e) {
            Log::error('Failed to extract data from:'.$input.'. Error: '.$e->getMessage());

            return null;
        }
    }
}
