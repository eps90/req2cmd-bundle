<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Action;

use Eps\Req2CmdBundle\Action\ApiResponderAction;
use Eps\Req2CmdBundle\CommandBus\CommandBusInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponderActionTest extends TestCase
{
    /**
     * @var ApiResponderAction
     */
    private $action;

    /**
     * @var CommandBusInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->createMock(CommandBusInterface::class);
        $this->action = new ApiResponderAction($this->commandBus);
    }

    /**
     * @test
     */
    public function itShouldSendACommandToTheCommandBus(): void
    {
        $requestedCommand = new \stdClass();
        $request = new Request();
        $request->attributes->set('_command', $requestedCommand);

        $this->commandBus->expects(static::once())
            ->method('handleCommand')
            ->with($requestedCommand);

        call_user_func($this->action, $request);
    }

    /**
     * @test
     */
    public function itShouldThrowWhenCommandIsNotSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidRequest = new Request();

        call_user_func($this->action, $invalidRequest);
    }

    /**
     * @test
     */
    public function itShouldReturn200ResponseForGetRequests(): void
    {
        $request = $this->createValidRequest(Request::METHOD_GET);

        $actualResponse = call_user_func($this->action, $request);
        $expectedResponse = new Response('', Response::HTTP_OK);

        static::assertEquals($expectedResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function itShouldReturn201ForPostRequests(): void
    {
        $request = $this->createValidRequest(Request::METHOD_POST);

        $actualResponse = call_user_func($this->action, $request);
        $expectedResponse = new Response('', Response::HTTP_CREATED);

        static::assertEquals($expectedResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function itShouldReturn200ForPutRequests(): void
    {
        $request = $this->createValidRequest(Request::METHOD_PUT);

        $actualResponse = call_user_func($this->action, $request);
        $expectedResponse = new Response('', Response::HTTP_OK);

        static::assertEquals($expectedResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function itShouldReturn204ForDeleteRequests(): void
    {
        $request = $this->createValidRequest(Request::METHOD_DELETE);

        $actualResponse = call_user_func($this->action, $request);
        $expectedResponse = new Response('', Response::HTTP_NO_CONTENT);

        static::assertEquals($expectedResponse, $actualResponse);
    }

    private function createValidRequest(string $method): Request
    {
        $request = new Request([], [], ['_command' => new \stdClass()]);
        $request->setMethod($method);

        return $request;
    }
}
