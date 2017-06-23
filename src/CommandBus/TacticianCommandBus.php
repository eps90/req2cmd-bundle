<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandBus;

use League\Tactician\CommandBus;

class TacticianCommandBus implements CommandBusInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handleCommand($command): void
    {
        $this->commandBus->handle($command);
    }
}
