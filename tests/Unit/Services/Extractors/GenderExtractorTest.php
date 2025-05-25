<?php

namespace Tests\Unit\Services\Extractors;

use App\Services\Extractors\GenderExtractor;
use GenderDetector\Gender;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenderExtractorTest extends TestCase
{
    private GenderExtractor $genderExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        // resolve it from the container so any mocks we register take effect
        $this->genderExtractor = app(GenderExtractor::class);
    }

    #[Test]
    #[DataProvider('genderProvider')]
    public function it_detects_gender_correctly(
        array $inputData,
        bool $shouldCallGenderDetector,
        Gender $detectorReturn,
        string $expectedGender
    ): void {
        $real = new \GenderDetector\GenderDetector;

        $fake = Mockery::mock($real)
            ->shouldReceive('getGender')
            ->times($shouldCallGenderDetector ? 1 : 0)
            ->with(Mockery::any())
            ->andReturn($detectorReturn)
            ->getMock();

        $this->app->instance(\GenderDetector\GenderDetector::class, $fake);
        $result = ($this->genderExtractor)(
            $inputData,
            fn (array $d) => $d
        );

        $this->assertSame(
            $expectedGender,
            $result['gender'] ?? null
        );
    }

    public static function genderProvider(): array
    {
        return [
            'preset gender is preserved' => [
                ['gender' => 'female', 'firstname' => 'John'],
                false,
                Gender::Male,
                'female',
            ],
            'detect female from firstname' => [
                ['firstname' => 'Alice'],
                true,
                Gender::Female,
                'female',
            ],
            'detect female from lastname fallback' => [
                ['lastname' => 'Alice'],
                true,
                Gender::MostlyFemale,
                'female',
            ],
            'detect male from firstname' => [
                ['firstname' => 'Bob'],
                true,
                Gender::Male,
                'male',
            ],
            'unknown maps to male' => [
                ['firstname' => 'X'],
                true,
                Gender::Unisex,
                'male',
            ],
            'no name defaults to male' => [
                [],
                false,
                Gender::Female,
                'male',
            ],
        ];
    }
}
