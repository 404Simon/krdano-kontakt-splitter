<?php

namespace App\Livewire;

use Exception;
use GenderDetector\Gender;
use GenderDetector\GenderDetector;
use Illuminate\Support\Facades\Log;
use LanguageDetection\Language;
use Livewire\Component;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
use TheIconic\NameParser\Language\English;
use TheIconic\NameParser\Language\German;
use TheIconic\NameParser\Parser;

class KontaktSplitter extends Component
{
    public string $unstructured = '';

    public ?array $structured = null;

    protected array $rules = [
        'unstructured' => [
            'required',
            // 'string',
            // 'not_regex:/[a-zA-Z]/',
            // 'regex:/^[0-9+\-\s()]+$/',
        ],
    ];

    public function retrieveDetailsByAI(string $input)
    {
        $schema = new ObjectSchema(
            name: 'person',
            description: 'Details about a persons name. German is default, only do other language, if youre sure. Dont complete public figures names if their name is not fully given',
            properties: [
                new StringSchema('salutation', 'salutation in the language of the person, like Herr, Frau, Mr, Mrs, Señor, Señora, etc.', true),
                new StringSchema('title', 'title like Dr., Dr. rer. nat., Prof., etc.', true),
                new StringSchema('gender', 'gender like male or female', true),
                new StringSchema('firstname', 'firstname(s)', true),
                new StringSchema('lastname', 'lastname, if there are multiple lastnames, concat them with -', true),
                new StringSchema('language', 'estimate the language based on the name (e.g. DE, FR, RU), leave null if not sure', true),
            ],
        );

        try {
            $response = Prism::structured()
                ->using(Provider::DeepSeek, 'deepseek-chat')
                ->withSchema($schema)
                ->withPrompt($input)
                ->asStructured();

            $structuredResponse = $response->structured;

            $structuredResponse['letter_salutation'] = $this->generateLetterSalutation($structuredResponse);

            return $structuredResponse;
        } catch (Exception $e) {
            Log::error('Failed to extract data from:'.$input.'. Error: '.$e->getMessage());

            return null;
        }
    }

    public function submit(): void
    {
        $this->validateOnly('unstructured');
        try {
            $this->structured = $this->retrieveDetails($this->unstructured);
        } catch (Exception $th) {
            $this->structured = null;
        }
    }

    public function save(): void
    {
        if (! $this->structured) {
            return;
        }

        auth()->user()
            ->savedInputs()
            ->create($this->structured);

        session()->flash('status', 'Briefanrede wurde gespeichert!');
    }

    public function reevaluateUsingAI(): void
    {
        $this->validateOnly('unstructured');
        try {
            $this->structured = $this->retrieveDetailsByAI($this->unstructured);
        } catch (Exception $th) {
            $this->structured = null;
        }
    }

    public function retrieveDetails(string $input): ?array
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

            $structuredResponse['letter_salutation'] = $this->generateLetterSalutation($structuredResponse);

            return $structuredResponse;

        } catch (\Exception $e) {
            Log::error('Failed to extract data from:'.$input.'. Error: '.$e->getMessage());

            return null;
        }
    }

    /*
    * Generates the letter salutation based on the structured data when lastname and salutation are set
    */
    public static function generateLetterSalutation(array $structured): string
    {
        if ($structured and isset($structured['lastname']) and isset($structured['salutation'])) {
            if (isset($structured['language'])) {
                if ($structured['language'] === 'DE') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Sehr geehrter';
                        } else {
                            $greeting = 'Sehr geehrte';
                        }
                    } else {
                        $greeting = 'Sehr geehrte Damen und Herren';
                    }
                } elseif ($structured['language'] === 'EN') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Dear Mr.';
                        } else {
                            $greeting = 'Dear Mrs.';
                        }
                    } else {
                        $greeting = 'Dear Sirs';
                    }
                } elseif ($structured['language'] === 'IT') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Egregio Signor';
                        } else {
                            $greeting = 'Gentile Signora';
                        }
                    } else {
                        $greeting = 'Egregi Signori';
                    }
                } elseif ($structured['language'] === 'FR') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Monsieur';
                        } else {
                            $greeting = 'Madame';
                        }
                    } else {
                        $greeting = 'Messiersdames';
                    }
                } elseif ($structured['language'] === 'ES') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Estimado';
                        } else {
                            $greeting = 'Estimada';
                        }
                    } else {
                        $greeting = 'Estimados Señores y Señoras';
                    }
                }
            } else {
                // Default country is germany
                if (isset($structured['gender'])) {
                    if ($structured['gender'] == 'male') {
                        $greeting = 'Sehr geehrter';
                    } else {
                        $greeting = 'Sehr geehrte';
                    }
                } else {
                    $greeting = 'Sehr geehrte Damen und Herren';
                }

            }
            if (isset($structured['title'])) {
                return "$greeting ".$structured['salutation'].' '.$structured['title'].' '.$structured['lastname'];
            } else {
                return "$greeting ".$structured['salutation'].' '.$structured['lastname'];
            }
        }

        return 'Sehr geehrte Damen und Herren';
    }
}
