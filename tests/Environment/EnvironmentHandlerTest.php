<?php
declare(strict_types=1);

namespace Szemul\Config\Test\Environment;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use josegonzalez\Dotenv\Loader;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Szemul\Config\Environment\EnvironmentHandler;
use PHPUnit\Framework\TestCase;
use Szemul\Config\Exception\MissingEnvValueException;

class EnvironmentHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const ENV_VALUES = [
        'TEST_ENV' => 'valueEnv',
    ];

    private const FILE_VALUES = [
        'TEST_FILE_BOOL'   => true,
        'TEST_FILE_NULL'   => null,
        'TEST_FILE_STRING' => 'test',
        'TEST_FILE_INT'    => 1,
        'TEST_FILE_FLOAT'  => 0.5,
    ];

    public function testCreateWithNoPaths_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EnvironmentHandler();
    }

    public function testGetValueWithMissingValueAndNoDefault_shouldThrowException(): void
    {
        $this->expectException(MissingEnvValueException::class);

        $this->getHandler()->getValue('MISSING_VALUE');
    }

    /** @dataProvider getDefaultValue */
    public function testGetValueWithMissingValueWithDefault_shouldReturnDefault(
        null|string|bool|int|float $default,
    ): void {
        $this->assertSame($default, $this->getHandler()->getValue('MISSING_VALUE', $default));
    }

    public function testGetValueWithValidValueFromFile_shouldReturnValue(): void
    {
        $handler = $this->getHandler();

        foreach (self::FILE_VALUES as $key => $value) {
            $this->assertSame($value, $handler->getValue($key));
        }
    }

    public function testGetValueWithValidValueFromEnv_shouldReturnValue(): void
    {
        $handler = $this->getHandler();

        foreach (self::ENV_VALUES as $key => $value) {
            $this->assertSame($value, $handler->getValue($key));
        }
    }

    /** @return array[] */
    #[Pure]
    public function getDefaultValue(): array
    {
        return [
            [null],
            [false],
            [true],
            [''],
            [0],
        ];
    }

    private function getHandler(): EnvironmentHandler
    {
        $paths = [
            '/var/www/html/.env',
            '/var/www/html/.env.test',
        ];

        /** @var Loader|MockInterface|LegacyMockInterface $loader */
        $loader = Mockery::mock(Loader::class);

        // @phpstan-ignore-next-line
        $loader->shouldReceive('load')
            ->ordered('load')
            ->once()
            ->with(['filepath' => $paths])
            ->andReturnSelf();

        // @phpstan-ignore-next-line
        $loader->shouldReceive('toArray')
            ->ordered('load')
            ->once()
            ->withNoArgs()
            ->andReturn(self::FILE_VALUES);

        $handler =  new EnvironmentHandler(...$paths);

        $handler->setEnvData(self::ENV_VALUES);
        $handler->setLoader($loader);

        return $handler;
    }
}
