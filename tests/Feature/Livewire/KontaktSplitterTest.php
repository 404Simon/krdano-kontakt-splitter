<?php

namespace Tests\Feature\Livewire;

use App\Livewire\KontaktSplitter;
use Livewire\Livewire;
use Tests\TestCase;

class KontaktSplitterTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(KontaktSplitter::class)
            ->assertStatus(200);
    }

    public function test_gender_change_should_change_salutation()
    {
        config([
            'languages.salutation' => [
                'DE' => [
                    'male' => 'Herr',
                    'female' => 'Frau',
                ],
            ],
            'languages.default_language' => 'DE',
        ]);

        $component = Livewire::test(KontaktSplitter::class)
            ->set('structured', [
                'salutation' => 'Herr',
                'title' => 'Dr.',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'gender' => 'male',
                'language' => 'DE',
            ])
            ->set('structured.gender', 'female')
            ->assertSet('structured.salutation', 'Frau');
    }

    public function test_gender_change_without_language_should_change_salutation_with_default_language()
    {
        config([
            'languages.salutation' => [
                'DE' => [
                    'male' => 'Herr',
                    'female' => 'Frau',
                ],
            ],
            'languages.default_language' => 'DE',
        ]);

        $component = Livewire::test(KontaktSplitter::class)
            ->set('structured', [
                'salutation' => 'Herr',
                'title' => 'Dr.',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'gender' => 'male',
            ])
            ->set('structured.gender', 'female')
            ->assertSet('structured.salutation', 'Frau');
    }
}
