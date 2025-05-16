<?php

namespace Tests\Feature\Livewire;

use App\Livewire\KontaktSplitter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KontaktSplitterTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(KontaktSplitter::class)
            ->assertStatus(200);
    }

    public function test_should_return_correct_letter_salutation()
    {
        $letterSalutation = KontaktSplitter::generateLetterSalutation([
            'salutation' => 'Herr',
            'title' => 'Dr.',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'gender' => 'male',
            'language' => 'DE',
        ]);

        $this->assertEquals('Sehr geehrter Herr Dr. Mustermann', $letterSalutation);
    }

    #[DataProvider('nameProvider')]
    public function test_add(array $structuredData, string $expected): void
    {
        $letterSalutation = KontaktSplitter::generateLetterSalutation($structuredData);

        $this->assertEquals($expected, $letterSalutation);
    }

    public static function nameProvider(): array
    {
        return [
            'deutscher Name' => [[
                'salutation' => 'Herr',
                'title' => 'Dr.',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'gender' => 'male',
                'language' => 'DE',
            ], 'Sehr geehrter Herr Dr. Mustermann'],
            'spanischer Name' => [[
                'salutation' => 'Señor',
                'firstname' => 'Juan',
                'lastname' => 'Pérez',
                'gender' => 'male',
                'language' => 'ES',
            ], 'Estimado Señor Juan Pérez'],
        ];
    }
}
