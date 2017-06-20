<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle;

use Eps\Req2CmdBundle\DependencyInjection\Req2CmdExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class Req2CmdBundle extends Bundle
{
    public function getContainerExtension(): Extension
    {
        if ($this->extension === null) {
            $this->extension = new Req2CmdExtension();
        }

        return $this->extension;
    }
}
