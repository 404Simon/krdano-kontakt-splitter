<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class AIService
{
    public function extractDetails(string $input): array
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

            return $structuredResponse;
        } catch (Exception $e) {
            Log::error('Failed to extract data from:'.$input.'. Error: '.$e->getMessage());

            return null;
        }
    }
}
