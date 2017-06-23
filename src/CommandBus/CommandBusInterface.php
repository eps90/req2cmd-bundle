<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandBus;

interface CommandBusInterface
{
    public function handleCommand($command): void;
}
