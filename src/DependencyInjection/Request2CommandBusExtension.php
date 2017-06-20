<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Request2CommandBusExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('actions.xml');
        $loader->load('extractors.xml');
        $loader->load('listeners.xml');
    }

    public function getAlias(): string
    {
        return 'req2cmd';
    }
}
