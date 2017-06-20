<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Command;

interface DeserializableCommandInterface
{
    public static function fromArray(array $commandProps);
}
