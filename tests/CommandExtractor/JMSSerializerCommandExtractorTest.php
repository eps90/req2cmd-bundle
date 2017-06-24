<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\CommandExtractor;

use Eps\Req2CmdBundle\CommandExtractor\JMSSerializerCommandExtractor;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyComplexCommand;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyId;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JMSSerializerCommandExtractorTest extends TestCase
{
    /**
     * @var JMSSerializerCommandExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../Fixtures/JMSSerializer', 'Eps\Req2CmdBundle\Tests\Fixtures')
            ->setDebug(true)
            ->build();

        $this->extractor = new JMSSerializerCommandExtractor($serializer, $serializer);
    }

    /**
     * @test
     */
    public function itShouldDeserializeCommandFromARequest(): void
    {
        $commandClass = DummyComplexCommand::class;
        $requestContent = json_encode([
            'dummy_id' => ['id_value' => 312],
            'name' => 'MyName',
            'date' => '2015-01-01 12:00:00'
        ]);
        $request = new Request([], [], [], [], [], [], $requestContent);
        $request->setRequestFormat('json');

        $expectedCommand = new DummyComplexCommand(
            new DummyId(312),
            'MyName',
            new \DateTime('2015-01-01 12:00:00')
        );
        $actualCommand = $this->extractor->extractFromRequest($request, $commandClass);

        static::assertEquals($expectedCommand, $actualCommand);
    }

    /**
     * @test
     */
    public function itShouldAllowToAppendAdditionalProperties(): void
    {
        $commandClass = DummyComplexCommand::class;
        $requestContent = json_encode([
            'dummy_id' => ['id_value' => 312],
            'date' => '2015-01-01 12:00:00'
        ]);
        $additionalProperties = [
            'name' => 'MyName'
        ];
        $request = new Request([], [], [], [], [], [], $requestContent);
        $request->setRequestFormat('json');

        $expectedCommand = new DummyComplexCommand(
            new DummyId(312),
            'MyName',
            new \DateTime('2015-01-01 12:00:00')
        );
        $actualCommand = $this->extractor->extractFromRequest($request, $commandClass, $additionalProperties);

        static::assertEquals($expectedCommand, $actualCommand);
    }
}
