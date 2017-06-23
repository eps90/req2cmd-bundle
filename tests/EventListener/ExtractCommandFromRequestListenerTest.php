<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\EventListener;

use Eps\Req2CmdBundle\CommandExtractor\MockCommandExtractor;
use Eps\Req2CmdBundle\EventListener\ExtractCommandFromRequestListener;
use Eps\Req2CmdBundle\Params\ParamCollector\ParamCollectorInterface;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExtractCommandFromRequestListenerTest extends TestCase
{
    /**
     * @var ExtractCommandFromRequestListener
     */
    private $listener;

    /**
     * @var MockCommandExtractor
     */
    private $extractor;

    /**
     * @var ParamCollectorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new MockCommandExtractor();
        $this->collector = $this->createMock(ParamCollectorInterface::class);
        $this->listener = new ExtractCommandFromRequestListener($this->extractor, $this->collector);
    }

    /**
     * @test
     */
    public function itShouldExtractCommandFromARequest(): void
    {
        $request = $this->getValidRequest();
        $event = $this->createGetResponseEvent($request);
        $deserializedCommand = new DummyCommand('My command', ['a' => 1]);
        $this->extractor->willReturn($deserializedCommand);

        $this->listener->onKernelRequest($event);

        $expectedCommand = $deserializedCommand;
        $actualCommand = $request->attributes->get(ExtractCommandFromRequestListener::CMD_PARAM);

        static::assertEquals($expectedCommand, $actualCommand);
    }

    /**
     * @test
     */
    public function itShouldCollectAdditionalParamsFromRoute(): void
    {
        $additionalParams = [
            'path' => [
                'id' => 'article_id'
            ]
        ];

        $request = $this->getValidRequest();
        $request->attributes->set(ExtractCommandFromRequestListener::CMD_PROPS_PARAM, $additionalParams);
        $event = $this->createGetResponseEvent($request);

        $collectedParams = ['article_id' => 321];
        $this->collector->expects($this->once())
            ->method('collect')
            ->with($request, $additionalParams)
            ->willReturn($collectedParams);

        $deserializedCommand = new DummyCommand('My name', ['a' => 1]);
        $this->extractor
            ->forArguments($request, DummyCommand::class, $collectedParams)
            ->willReturn($deserializedCommand);

        $this->listener->onKernelRequest($event);

        $expectedCommand = $deserializedCommand;
        $actualCommand = $request->attributes->get(ExtractCommandFromRequestListener::CMD_PARAM);

        static::assertEquals($expectedCommand, $actualCommand);
    }

    /**
     * @test
     */
    public function itShouldSetAControllerForCommandRequest(): void
    {
        $request = $this->getValidRequest();
        $event = $this->createGetResponseEvent($request);

        $this->listener->onKernelRequest($event);

        $expectedRoute = ExtractCommandFromRequestListener::API_RESPONDER_CONTROLLER;
        $actualRoute = $request->get('_controller');

        static::assertEquals($expectedRoute, $actualRoute);
    }

    /**
     * @test
     */
    public function itShouldNotOverwriteControllerIfThereIsConfiguredOne(): void
    {
        $request = $this->getValidRequest();
        $ctrlFromParams = 'AppBundle:default';
        $request->attributes->set('_controller', $ctrlFromParams);
        $event = $this->createGetResponseEvent($request);

        $this->listener->onKernelRequest($event);

        static::assertEquals($ctrlFromParams, $request->attributes->get('_controller'));
    }

    /**
     * @test
     */
    public function itShouldCleanUpCommandClassParameter(): void
    {
        $request = $this->getValidRequest();
        $event = $this->createGetResponseEvent($request);

        $this->listener->onKernelRequest($event);

        static::assertFalse($request->attributes->has(ExtractCommandFromRequestListener::CMD_CLASS_PARAM));
    }

    /**
     * @test
     */
    public function itShouldDoNothingWhenCommandClassIsNotProvided(): void
    {
        $request = $this->getValidRequest();
        $request->attributes->remove(ExtractCommandFromRequestListener::CMD_CLASS_PARAM);
        $event = $this->createGetResponseEvent($request);

        $this->listener->onKernelRequest($event);

        static::assertFalse($request->attributes->has(ExtractCommandFromRequestListener::CMD_PARAM));
    }

    private function createGetResponseEvent(Request $request): GetResponseEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = HttpKernelInterface::MASTER_REQUEST;

        return new GetResponseEvent($kernel, $request, $requestType);
    }

    private function getValidRequest(): Request
    {
        $requestBody = json_encode([
            'name' => 'My command',
            'opts' => ['a' => 1]
        ]);
        $requestAttributes = [
            '_command_class' => DummyCommand::class
        ];
        $request = new Request([], [], $requestAttributes, [], [], [], $requestBody);
        $request->setRequestFormat('json');

        return $request;
    }
}
