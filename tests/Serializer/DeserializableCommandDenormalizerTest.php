<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\Tests\Serializer;

use Eps\Request2CommandBusBundle\Serializer\DeserializableCommandDenormalizer;
use Eps\Request2CommandBusBundle\Tests\Fixtures\DummyDeserializableCommand;
use PHPUnit\Framework\TestCase;

class DeserializableCommandDenormalizerTest extends TestCase
{
    /**
     * @var DeserializableCommandDenormalizer
     */
    private $denormalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->denormalizer = new DeserializableCommandDenormalizer();
    }

    /**
     * @test
     */
    public function itShouldSupportOnlySerializableCommandClass(): void
    {
        $supportedClass = DummyDeserializableCommand::class;
        $unsupportedClass = \stdClass::class;

        static::assertTrue($this->denormalizer->supportsDenormalization([], $supportedClass));
        static::assertFalse($this->denormalizer->supportsDenormalization([], $unsupportedClass));
    }

    /**
     * @test
     */
    public function itShouldCallStaticConstructorOnSerializableCommand(): void
    {
        $denormalizedData = [
            'name' => 'My command',
            'opts' => [
                'a' => 1,
                'b' => 32
            ]
        ];
        $className = DummyDeserializableCommand::class;

        $actualResult = $this->denormalizer->denormalize($denormalizedData, $className);
        $expectedResult = new DummyDeserializableCommand('My command', ['a' => 1, 'b' => 32]);

        static::assertEquals($expectedResult, $actualResult);
    }
}
