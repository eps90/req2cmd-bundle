<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle;

use Eps\Req2CmdBundle\DependencyInjection\CompilerPass\ParamMapperPass;
use Eps\Req2CmdBundle\DependencyInjection\Req2CmdExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
final class Req2CmdBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ParamMapperPass());
    }

    public function getContainerExtension(): Extension
    {
        if ($this->extension === null) {
            $this->extension = new Req2CmdExtension();
        }

        return $this->extension;
    }

}
