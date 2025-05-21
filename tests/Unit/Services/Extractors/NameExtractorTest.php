<?php

namespace Tests\Unit\Services\Extractors;

use App\Services\Extractors\NameExtractor;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class NameExtractorTest extends TestCase
{
    private NameExtractor $nameExtractor;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('languages.lastname_prefixes', ['von', 'van']);

        $this->nameExtractor = new NameExtractor;
    }

    #[Test]
    #[DataProvider('nameProvider')]
    public function it_extracts_names_correctly(
        string $input,
        ?string $expectedFirstname,
        ?string $expectedLastname
    ): void {
        $data = ['remaining' => $input];

        $result = ($this->nameExtractor)($data, function ($processedData) {
            return $processedData;
        });

        $this->assertEquals($expectedFirstname, $result['firstname'] ?? null);
        $this->assertEquals($expectedLastname, $result['lastname'] ?? null);
    }

    public static function nameProvider(): array
    {
        return [
            'empty string' => [
                'input' => '',
                'expectedFirstname' => null,
                'expectedLastname' => null,
            ],
            'single word' => [
                'input' => 'Smith',
                'expectedFirstname' => null,
                'expectedLastname' => 'Smith',
            ],
            'two words' => [
                'input' => 'John Smith',
                'expectedFirstname' => 'John',
                'expectedLastname' => 'Smith',
            ],
            'three words without prefix' => [
                'input' => 'John Michael Smith',
                'expectedFirstname' => 'John Michael',
                'expectedLastname' => 'Smith',
            ],
            'three words with von prefix' => [
                'input' => 'Felix von Hohenzollern',
                'expectedFirstname' => 'Felix',
                'expectedLastname' => 'von Hohenzollern',
            ],
            'four words with van prefix' => [
                'input' => 'Martin van der Helm',
                'expectedFirstname' => 'Martin',
                'expectedLastname' => 'van der Helm',
            ],
            'multiple prefixes' => [
                'input' => 'Anna von van Beethoven',
                'expectedFirstname' => 'Anna',
                'expectedLastname' => 'von van Beethoven',
            ],
            'prefix at beginning' => [
                'input' => 'von Trapp Family',
                'expectedFirstname' => 'von Trapp',
                'expectedLastname' => 'Family',
            ],
            'hyphenated last name' => [
                'input' => 'Sarah Mueller-Meiser',
                'expectedFirstname' => 'Sarah',
                'expectedLastname' => 'Mueller-Meiser',
            ],
            'extra spaces' => [
                'input' => '  John   Smith  ',
                'expectedFirstname' => 'John',
                'expectedLastname' => 'Smith',
            ],
        ];
    }
}
