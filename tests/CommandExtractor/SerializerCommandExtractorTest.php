<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\Tests\CommandExtractor;

use Eps\Request2CommandBusBundle\CommandExtractor\SerializerCommandExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerCommandExtractorTest extends TestCase
{
    /**
     * @var SerializerCommandExtractor
     */
    private $extractor;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->extractor = new SerializerCommandExtractor($this->serializer);
    }

    /**
     * @test
     */
    public function itShouldDeserializeRequestUsingSerializer(): void
    {
        $commandClass = 'MyClass';
        $requestContent = json_encode([
            'my' => 'command'
        ]);
        $request = new Request([], [], [], [], [], [], $requestContent);
        $requestedFormat = 'json';
        $request->setRequestFormat($requestedFormat);

        $deserializedObj = new \stdClass('deseriazlied_object');
        $this->serializer->expects(static::once())
            ->method('deserialize')
            ->with($requestContent, $commandClass, $requestedFormat)
            ->willReturn($deserializedObj);

        $actualResult = $this->extractor->extractFromRequest($request, $commandClass);
        $expectedResult = $deserializedObj;

        static::assertEquals($expectedResult, $actualResult);
    }
}
