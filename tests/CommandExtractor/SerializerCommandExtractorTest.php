<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\CommandExtractor;

use Eps\Req2CmdBundle\CommandExtractor\SerializerCommandExtractor;
use Eps\Req2CmdBundle\Serializer\DeserializableCommandDenormalizer;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyDeserializableCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class SerializerCommandExtractorTest extends TestCase
{
    /**
     * @var SerializerCommandExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = new Serializer(
            [new DeserializableCommandDenormalizer()],
            [new JsonEncoder()]
        );

        $this->extractor = new SerializerCommandExtractor($serializer, $serializer, $serializer);
    }

    /**
     * @test
     */
    public function itShouldDeserializeRequestUsingSerializer(): void
    {
        $commandClass = DummyDeserializableCommand::class;
        $requestContent = json_encode([
            'name' => 'My command',
            'opts' => [
                'a' => 1,
                'b' => true
            ]
        ]);
        $request = new Request([], [], [], [], [], [], $requestContent);
        $request->setRequestFormat('json');

        $expectedResult = new DummyDeserializableCommand('My command', ['a' => 1, 'b' => true]);
        $actualResult = $this->extractor->extractFromRequest($request, $commandClass);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldAllowToAddAdditionalProperties(): void
    {
        $commandClass = DummyDeserializableCommand::class;
        $requestedContent = json_encode([
            'opts' => ['a' => 1]
        ]);
        $additionalProps = [
            'name' => 'My command'
        ];
        $request = new Request([], [], [], [], [], [], $requestedContent);
        $request->setRequestFormat('json');

        $expectedResult = new DummyDeserializableCommand('My command', ['a' => 1]);
        $actualResult = $this->extractor->extractFromRequest($request, $commandClass, $additionalProps);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldOutputAnEmptyArrayIfBodyIsEmpty(): void
    {
        $commandClass = DummyDeserializableCommand::class;
        $request = new Request();
        $request->setRequestFormat('json');

        $expectedResult = new DummyDeserializableCommand('', []);
        $actualResult = $this->extractor->extractFromRequest($request, $commandClass);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldUseOnlyAdditionalPropsWhenBodyIsEmpty(): void
    {
        $commandClass = DummyDeserializableCommand::class;
        $additionalProps = [
            'name' => 'My command'
        ];
        $request = new Request();
        $request->setRequestFormat('json');

        $expectedResult = new DummyDeserializableCommand('My command', []);
        $actualResult = $this->extractor->extractFromRequest($request, $commandClass, $additionalProps);

        static::assertEquals($expectedResult, $actualResult);
    }
}
