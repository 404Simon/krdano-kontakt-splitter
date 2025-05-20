<?php

namespace Tests\Feature;

use App\Services\LetterSalutationService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LetterSalutationServiceTest extends TestCase
{
    #[DataProvider('nameProvider')]
    public function test_letter_salutation_generation(array $structuredData, string $expected): void
    {
        $letterSalutation = LetterSalutationService::generate($structuredData);

        $this->assertEquals($expected, $letterSalutation);
    }

    public static function nameProvider(): array
    {
        return [
            'keine Daten' => [[], 'Sehr geehrte Damen und Herren'],
            'keine Sprache' => [[
                'salutation' => 'Herr',
                'title' => 'Dr.',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'gender' => 'male',
            ], 'Sehr geehrter Herr Dr. Mustermann'],
            'kein Geschlecht' => [[
                'title' => 'Dr.',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'language' => 'DE',
            ], 'Sehr geehrte Damen und Herren Dr. Mustermann'],
            'kein Titel' => [[
                'salutation' => 'Herr',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'gender' => 'male',
                'language' => 'DE',
            ], 'Sehr geehrter Herr Mustermann'],
            'kein Nachname' => [[
                'salutation' => 'Herr',
                'title' => 'Dr.',
                'firstname' => 'Max',
                'gender' => 'male',
                'language' => 'DE',
            ], 'Sehr geehrter Herr Dr.'],
            'männlicher deutscher Name' => [[
                'salutation' => 'Herr',
                'title' => 'Dr.',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'gender' => 'male',
                'language' => 'DE',
            ], 'Sehr geehrter Herr Dr. Mustermann'],
            'weiblicher deutscher Name' => [[
                'salutation' => 'Frau',
                'title' => 'Prof.',
                'firstname' => 'Marlene',
                'lastname' => 'Musterfrau',
                'gender' => 'female',
                'language' => 'DE',
            ], 'Sehr geehrte Frau Prof. Musterfrau'],
            'spanischer Name' => [[
                'salutation' => 'Señor',
                'firstname' => 'Juan',
                'lastname' => 'Pérez',
                'gender' => 'male',
                'language' => 'ES',
            ], 'Estimado Señor Pérez'],
            'französischer Name' => [[
                'salutation' => 'Monsieur',
                'title' => 'Dr.',
                'firstname' => 'Emil',
                'lastname' => 'Lambert',
                'gender' => 'male',
                'language' => 'FR',
            ], 'Monsieur Dr. Lambert'],
        ];
    }
}
