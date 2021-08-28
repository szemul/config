<?php
declare(strict_types=1);

namespace Szemul\Config\Test;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use stdClass;
use Szemul\Config\Config;
use PHPUnit\Framework\TestCase;
use Szemul\Config\Exception\MissingConfigValueException;

class ConfigTest extends TestCase
{
    private Config $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new Config();
    }

    public function testSettingSingleValues_setsTheValue(): void
    {
        $values = [
            'test1' => 'value1',
            'test2' => 2,
            'test3' => false,
            'test4' => null,
            'test5' => [
                'foo' => 'bar',
            ],
            'test6' => new stdClass(),
        ];

        foreach ($values as $key => $value) {
            $this->assertSame($this->sut, $this->sut->set($key, $value));
        }

        $data = $this->sut->toArray();
        ksort($data);

        $this->assertSame($values, $data);
    }

    public function testSettingByArray_setsTheValue(): void
    {
        $values1 = [
            'test1' => 'value1',
            'test2' => 'value2.0',
        ];

        $values2 = [
            'test2' => 'value2.1',
            'test3' => 'value3',
        ];

        $expectedValues = [
            'test1' => 'value1',
            'test2' => 'value2.1',
            'test3' => 'value3',
        ];

        $this->assertSame($this->sut, $this->sut->setArray($values1));
        $this->assertSame($this->sut, $this->sut->setArray($values2));

        $data = $this->sut->toArray();

        ksort($data);

        $this->assertSame($expectedValues, $data);
    }

    public function testGettingAValueThatIsNotSetWithoutADefault_throwsException(): void
    {
        $this->expectException(MissingConfigValueException::class);

        $this->sut->get('test');
    }

    /**
     * @dataProvider getDefaultValue
     */
    public function testGettingAValueThatIsNotSetWithADefault_returnsDefault(mixed $default): void
    {
        $this->assertSame($default, $this->sut->get('test', $default));
    }

    public function testGettingASetValue_returnsTheValue(): void
    {
        $this->sut->set('test', 'value');

        $this->assertSame('value', $this->sut->get('test'));
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
            [new stdClass()],
            [['test' => 'value']],
        ];
    }

    public function testGetGetPrefixWithNoPrefix_shouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->getPrefix('');
    }

    public function testGetGetPrefixWithNonExistingPrefix_shouldThrowException(): void
    {
        $this->expectException(MissingConfigValueException::class);

        $dataToSet = [
            'test.test1'  => 'value1',
            'test.test2'  => 'value2',
            'test2.test3' => 'value3',
            'test4'       => 'value4',
        ];

        $this->sut->setArray($dataToSet);

        $this->sut->getPrefix('test4');
    }

    public function testGetGetPrefixWithAValidPrefix_shouldReturnDataAsArray(): void
    {
        $dataToSet = [
            'test.test1'  => 'value1',
            'test.test2'  => 'value2',
            'test2.test3' => 'value3',
        ];

        $expectedData = [
            'test1' => 'value1',
            'test2' => 'value2',
        ];

        $this->sut->setArray($dataToSet);

        $data = $this->sut->getPrefix('test');

        ksort($data);

        $this->assertSame($expectedData, $data);
    }

    public function testGetGetPrefixWithAValidPrefixEndingInDot_shouldReturnDataAsArray(): void
    {
        $dataToSet = [
            'test.test1'  => 'value1',
            'test.test2'  => 'value2',
            'test2.test3' => 'value3',
        ];

        $expectedData = [
            'test1' => 'value1',
            'test2' => 'value2',
        ];

        $this->sut->setArray($dataToSet);

        $data = $this->sut->getPrefix('test.');

        ksort($data);

        $this->assertSame($expectedData, $data);
    }

    public function testHas(): void
    {
        $this->sut->set('test', 'value');

        $this->assertTrue($this->sut->has('test'));
        $this->assertFalse($this->sut->has('test2'));
    }

    public function testHasPrefixWithAPrefix_shouldCheckIfThePrefixIsSet(): void
    {
        $this->sut->set('test.test1', 'value');
        $this->sut->set('test2', 'value');

        $this->assertTrue($this->sut->hasPrefix('test'));
        $this->assertFalse($this->sut->hasPrefix('test2'));
        $this->assertFalse($this->sut->hasPrefix('test3'));
    }

    public function testHasPrefixWithNoPrefix_shouldThrowAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->hasPrefix('');
    }
}
