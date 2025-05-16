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
                new StringSchema('letter_salutation', 'e.g. Sehr geehrter Herr Eris, Dear Mr..., etc.', true),
            ],
        );

        try {
            $response = Prism::structured()
                ->using(Provider::OpenAI, 'gpt-4o-mini')
                ->withSchema($schema)
                ->withPrompt('Du bist Informatikexperte. Analysiere die gegebene Frage und bestimme alle relevanten Teilgebiete der Informatik.\nFrage: '.$input)
                ->asStructured();

            $structuredResponse = $response->structured;

            return $structuredResponse;
        } catch (Exception $e) {
            Log::error('Failed to extract data from:'.$input.'. Error: '.$e->getMessage());

            return null;
        }
    }

    public function updatedUnstructured(): void
    {
        $this->validateOnly('unstructured');
        try {
            $this->structured = $this->retrieveDetails($this->unstructured);
        } catch (Exception $th) {
            $this->structured = null;
        }
    }
}
