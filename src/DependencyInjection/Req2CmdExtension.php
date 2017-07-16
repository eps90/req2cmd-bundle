<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class Req2CmdExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Req2CmdConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('actions.xml');
        $loader->load('extractors.xml');
        $loader->load('listeners.xml');
        $loader->load('param_mappers.xml');
        $loader->load('command_bus.xml');
        $loader->load('serializer.xml');

        $this->configureExtractors($config, $container);
        $this->configureCommandBus($config, $container);
        $this->configureEventListeners($config, $container);
    }

    public function getAlias(): string
    {
        return 'req2cmd';
    }

    private function configureExtractors(array $config, ContainerBuilder $container): void
    {
        $extractorId = (string)$config['extractor']['service_id'];
        $container->setAlias('eps.req2cmd.extractor', $extractorId);
    }

    private function configureCommandBus(array $config, ContainerBuilder $container): void
    {
        $commandBusId = (string)$config['command_bus']['service_id'];
        if ($commandBusId === 'eps.req2cmd.command_bus.tactician') {
            $busName = (string)$config['command_bus']['name'];
            $tacticianServiceName = 'tactician.commandbus.' . $busName;
            $busDefinition = $container->findDefinition('eps.req2cmd.command_bus.tactician');
            $busDefinition->replaceArgument(0, new Reference($tacticianServiceName));
        }

        $container->setAlias('eps.req2cmd.command_bus', $commandBusId);
    }

    private function configureEventListeners(array $config, ContainerBuilder $container): void
    {
        foreach ((array)$config['listeners'] as $listenerName => $listenerConfig) {
            $listenerId = $this->getListenerSvcIdByAlias($listenerName);

            if (!$listenerConfig['enabled']) {
                $container->removeDefinition($listenerId);
                continue;
            }

            $this->configureEventListener($container, $listenerId, $listenerConfig);
        }
    }

    private function configureEventListener(ContainerBuilder $container, string $listenerId, array $listenerCfg): void
    {
        $definition = $container->findDefinition($listenerId);
        $serviceTags = $definition->getTags();

        array_walk(
            $serviceTags['kernel.event_listener'],
            function (&$tag) use ($listenerCfg) {
                $tag['priority'] = $listenerCfg['priority'];
            }
        );

        $definition->setTags($serviceTags);
    }

    private function getListenerSvcIdByAlias(string $alias): string
    {
        $listenersMap = [
            'extractor' => 'eps.req2cmd.listener.extract_command'
        ];

        return $listenersMap[$alias];
    }
}
