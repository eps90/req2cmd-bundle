<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\CommandExtractor;

use Eps\Req2CmdBundle\CommandExtractor\SerializerCommandExtractor;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyCommand;
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
            'name' => 'My command',
            'opts' => [
                'a' => 1,
                'b' => true
            ]
        ]);
        $request = new Request([], [], [], [], [], [], $requestContent);
        $requestedFormat = 'json';
        $request->setRequestFormat($requestedFormat);

        $mappedCommand = new DummyCommand('My class', ['a' => 1, 'b' => true]);
        $this->serializer->expects(static::once())
            ->method('deserialize')
            ->with($requestContent, $commandClass, $requestedFormat)
            ->willReturn($mappedCommand);

        $actualResult = $this->extractor->extractFromRequest($request, $commandClass);
        $expectedResult = $mappedCommand;

        static::assertEquals($expectedResult, $actualResult);
    }
}
