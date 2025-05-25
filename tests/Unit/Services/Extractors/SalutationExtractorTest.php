<?php

namespace Tests\Unit\Services\Extractors;

use App\Services\Extractors\SalutationExtractor;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SalutationExtractorTest extends TestCase
{
    private SalutationExtractor $salutationExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('languages.salutation', [
            'en' => ['male' => 'mr', 'female' => 'ms'],
            'de' => ['male' => 'herr', 'female' => 'frau'],
            'fr' => ['male' => 'monsieur', 'female' => 'madame'],
        ]);

        Config::set('languages.specialSalutationVariations', [
            'mrs' => ['en', 'female'],
            'mlle' => ['fr', 'female'],
        ]);

        $this->salutationExtractor = new SalutationExtractor;
    }

    #[Test]
    #[DataProvider('salutationProvider')]
    public function it_extracts_salutations_correctly(
        string $input,
        ?string $expectedSalutation,
        ?string $expectedLanguage,
        ?string $expectedGender,
        string $expectedRemaining
    ): void {
        $data = ['remaining' => $input];
        $result = ($this->salutationExtractor)($data, fn ($d) => $d);

        if ($expectedSalutation !== null) {
            $this->assertEquals(
                $expectedSalutation,
                $result['salutation']
            );
            $this->assertEquals(
                $expectedLanguage,
                $result['language']
            );
            $this->assertEquals(
                $expectedGender,
                $result['gender']
            );
        } else {
            $this->assertArrayNotHasKey('salutation', $result);
            $this->assertArrayNotHasKey('language', $result);
            $this->assertArrayNotHasKey('gender', $result);
        }

        $this->assertEquals(
            $expectedRemaining,
            $result['remaining']
        );
    }

    public static function salutationProvider(): array
    {
        return [
            'empty string' => [
                'input' => '',
                'expectedSalutation' => null,
                'expectedLanguage' => null,
                'expectedGender' => null,
                'expectedRemaining' => '',
            ],
            'no salutation' => [
                'input' => 'John Doe',
                'expectedSalutation' => null,
                'expectedLanguage' => null,
                'expectedGender' => null,
                'expectedRemaining' => 'John Doe',
            ],
            'simple male' => [
                'input' => 'Mr John Doe',
                'expectedSalutation' => 'Mr',
                'expectedLanguage' => 'en',
                'expectedGender' => 'male',
                'expectedRemaining' => 'John Doe',
            ],
            'case insensitive' => [
                'input' => 'mR Jane Doe',
                'expectedSalutation' => 'mR',
                'expectedLanguage' => 'en',
                'expectedGender' => 'male',
                'expectedRemaining' => 'Jane Doe',
            ],
            'german female' => [
                'input' => 'Frau Anna Schmidt',
                'expectedSalutation' => 'Frau',
                'expectedLanguage' => 'de',
                'expectedGender' => 'female',
                'expectedRemaining' => 'Anna Schmidt',
            ],
            'french male' => [
                'input' => 'monsieur Dupont',
                'expectedSalutation' => 'monsieur',
                'expectedLanguage' => 'fr',
                'expectedGender' => 'male',
                'expectedRemaining' => 'Dupont',
            ],
            'special variation mrs' => [
                'input' => 'MRS Alice',
                'expectedSalutation' => 'MRS',
                'expectedLanguage' => 'en',
                'expectedGender' => 'female',
                'expectedRemaining' => 'Alice',
            ],
            'single salutation only' => [
                'input' => 'Mr',
                'expectedSalutation' => 'Mr',
                'expectedLanguage' => 'en',
                'expectedGender' => 'male',
                'expectedRemaining' => '',
            ],
        ];
    }
}
