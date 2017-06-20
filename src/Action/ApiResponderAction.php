<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\Action;

use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponderAction
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request): Response
    {
        $command = $request->attributes->get('_command');
        if ($command === null) {
            throw new \InvalidArgumentException('No _command argument found for action');
        }
        $this->commandBus->handle($command);

        return new Response('', $this->getResponseStatusCode($request));
    }

    private function getResponseStatusCode(Request $request): int
    {
        switch ($request->getMethod()) {
            case Request::METHOD_PUT:
                $statusCode = Response::HTTP_OK;
                break;
            case Request::METHOD_POST:
                $statusCode = Response::HTTP_CREATED;
                break;
            case Request::METHOD_DELETE:
                $statusCode = Response::HTTP_NO_CONTENT;
                break;
            default:
                $statusCode = Response::HTTP_OK;
        }

        return $statusCode;
    }
}
