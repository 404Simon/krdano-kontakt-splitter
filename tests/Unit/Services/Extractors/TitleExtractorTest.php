<?php

namespace Tests\Unit\Services\Extractors;

use App\Services\Extractors\TitleExtractor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TitleExtractorTest extends TestCase
{
    private TitleExtractor $titleExtractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->titleExtractor = new TitleExtractor;
    }

    #[Test]
    #[DataProvider('titleProvider')]
    public function it_extracts_titles_correctly(
        string $input,
        array $expectedTitles,
        string $expectedRemaining
    ): void {
        $data = ['remaining' => $input];

        $result = ($this->titleExtractor)($data, function ($processedData) {
            return $processedData;
        });

        $this->assertEquals($expectedTitles, $result['titles'] ?? []);
        $this->assertEquals($expectedRemaining, $result['remaining'] ?? '');
    }

    public static function titleProvider(): array
    {
        return [
            'empty string' => [
                'input' => '',
                'expectedTitles' => [],
                'expectedRemaining' => '',
            ],
            'no titles' => [
                'input' => 'John Smith',
                'expectedTitles' => [],
                'expectedRemaining' => 'John Smith',
            ],
            'single title' => [
                'input' => 'Prof John Smith',
                'expectedTitles' => ['Prof'],
                'expectedRemaining' => 'John Smith',
            ],
            'single title lowercase' => [
                'input' => 'prof John Smith',
                'expectedTitles' => ['prof'],
                'expectedRemaining' => 'John Smith',
            ],
            'multiple titles' => [
                'input' => 'Prof Dr John Smith',
                'expectedTitles' => ['Prof', 'Dr'],
                'expectedRemaining' => 'John Smith',
            ],
            'title with period' => [
                'input' => 'Dr. John Smith',
                'expectedTitles' => ['Dr.'],
                'expectedRemaining' => 'John Smith',
            ],
            'multiple titles with periods' => [
                'input' => 'Prof. Dr. John Smith',
                'expectedTitles' => ['Prof.', 'Dr.'],
                'expectedRemaining' => 'John Smith',
            ],
            'mixed titles with and without periods' => [
                'input' => 'Prof Dr. PhD John Smith',
                'expectedTitles' => ['Prof', 'Dr.', 'PhD'],
                'expectedRemaining' => 'John Smith',
            ],
            'titles with abbreviations' => [
                'input' => 'Dr. rer. nat. John Smith',
                'expectedTitles' => ['Dr.', 'rer.', 'nat.'],
                'expectedRemaining' => 'John Smith',
            ],
            'titles with h.c.' => [
                'input' => 'Prof. Dr. h.c. John Smith',
                'expectedTitles' => ['Prof.', 'Dr.', 'h.c.'],
                'expectedRemaining' => 'John Smith',
            ],
            'titles with non-standard abbreviations' => [
                'input' => 'M.D. Ph.D. John Smith',
                'expectedTitles' => ['M.D.', 'Ph.D.'],
                'expectedRemaining' => 'John Smith',
            ],
            'title after name should not be extracted' => [
                'input' => 'John Smith Prof',
                'expectedTitles' => [],
                'expectedRemaining' => 'John Smith Prof',
            ],
            'diplom ingenieur' => [
                'input' => 'Dipl. Ing. John Smith',
                'expectedTitles' => ['Dipl.', 'Ing.'],
                'expectedRemaining' => 'John Smith',
            ],
            'academic degrees' => [
                'input' => 'MSc BA PhD John Smith',
                'expectedTitles' => ['MSc', 'BA', 'PhD'],
                'expectedRemaining' => 'John Smith',
            ],
            'word ending with period that is not a title' => [
                'input' => 'Inc. John Smith',
                'expectedTitles' => ['Inc.'],
                'expectedRemaining' => 'John Smith',
            ],
            'title with non-title words in between should stop extraction' => [
                'input' => 'Prof John Dr Smith',
                'expectedTitles' => ['Prof'],
                'expectedRemaining' => 'John Dr Smith',
            ],
        ];
    }
}
