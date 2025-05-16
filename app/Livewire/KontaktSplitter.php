<?php

namespace App\Livewire;

use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

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

    public function retrieveDetails(string $input)
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
                ->using(Provider::OpenAI, 'gpt-4o-mini')
                ->withSchema($schema)
                ->withPrompt($input)
                ->asStructured();

            $structuredResponse = $response->structured;

            $structuredResponse['letter_salutation'] = $this->generateLetterSalutation($this->structuredResponse);

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
                            $greeting = 'Estimado Señor';
                        } else {
                            $greeting = 'Estimada Señora';
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
