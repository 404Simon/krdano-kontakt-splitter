<?php

namespace Tests\Unit\Services;

use App\Services\KontaktParserService;
use GenderDetector\Gender;
use GenderDetector\GenderDetector;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KontaktParserServiceTest extends TestCase
{
    private KontaktParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();

        // Force default language to DE and configure salutation map
        Config::set('language.default_language', 'DE');
        Config::set('languages.default_language', 'DE');
        Config::set('languages.salutation', [
            'DE' => ['male' => 'Herr', 'female' => 'Frau'],
        ]);

        // Resolve the parser from the container
        $this->parser = app(KontaktParserService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_throws_if_input_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->parser->extractDetails('   ');
    }

    #[Test]
    #[DataProvider('contactProvider')]
    public function it_parses_contact_details(
        string $input,
        bool $shouldCallGenderDetector,
        Gender $detectorReturn,
        array $expected
    ): void {
        // 1) Create a real GenderDetector and mock just getGender()
        $real = new GenderDetector;
        $fake = Mockery::mock($real)
            ->shouldReceive('getGender')
            ->times($shouldCallGenderDetector ? 1 : 0)
            ->with(Mockery::any())
            ->andReturn($detectorReturn)
            ->getMock();

        // 2) Bind our fake into the container so the service picks it up
        $this->app->instance(GenderDetector::class, $fake);

        // 3) Run extraction
        $result = $this->parser->extractDetails($input);

        // 4) Assert
        $this->assertSame($expected['salutation'], $result['salutation']);
        $this->assertSame($expected['title'], $result['title']);
        $this->assertSame($expected['firstname'], $result['firstname']);
        $this->assertSame($expected['lastname'], $result['lastname']);
        $this->assertSame($expected['gender'], $result['gender']);
        $this->assertSame('DE', $result['language']);
    }

    public static function contactProvider(): array
    {
        return [
            'simple male name' => [
                'Max Mustermann',
                true,
                Gender::Male,
                [
                    'salutation' => 'Herr',
                    'title' => '',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'gender' => 'male',
                ],
            ],
            'simple female name' => [
                'Anna Müller',
                true,
                Gender::Female,
                [
                    'salutation' => 'Frau',
                    'title' => '',
                    'firstname' => 'Anna',
                    'lastname' => 'Müller',
                    'gender' => 'female',
                ],
            ],
            'with preset salutation and titles' => [
                'Herr Prof. Dr. Max Mustermann',
                false,
                Gender::Male,
                [
                    // salutation preserved
                    'salutation' => 'Herr',
                    'title' => 'Prof. Dr.',
                    'firstname' => 'Max',
                    'lastname' => 'Mustermann',
                    'gender' => 'male',
                ],
            ],
            'only title + female firstname' => [
                'Dr. Maria Schmitt',
                true,
                Gender::Female,
                [
                    // no salutation in input, filled from gender
                    'salutation' => 'Frau',
                    'title' => 'Dr.',
                    'firstname' => 'Maria',
                    'lastname' => 'Schmitt',
                    'gender' => 'female',
                ],
            ],
            'firstname only defaults male' => [
                'Karl',
                true,
                Gender::Male,
                [
                    'salutation' => 'Herr',
                    'title' => '',
                    'firstname' => null,
                    'lastname' => 'Karl',
                    'gender' => 'male',
                ],
            ],
            'firstname only female' => [
                'Anna',
                true,
                Gender::Female,
                [
                    'salutation' => 'Frau',
                    'title' => '',
                    'firstname' => null,
                    'lastname' => 'Anna',
                    'gender' => 'female',
                ],
            ],
            'compound lastname with prefix' => [
                'Prof. Dr. phil. Anna von der Leyen',
                true,
                Gender::Female,
                [
                    'salutation' => 'Frau',
                    'title' => 'Prof. Dr. phil.',
                    'firstname' => 'Anna',
                    'lastname' => 'von der Leyen',
                    'gender' => 'female',
                ],
            ],
        ];
    }
}
