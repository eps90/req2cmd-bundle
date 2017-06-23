<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Req2CmdConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder();

        $root = $builder->root('req2cmd');
        $root
            ->children()
                ->append($this->addExtractorNode())
                ->append($this->addCommandBusNode())
                ->append($this->addEventListenersNode())
            ->end();

        return $builder;
    }

    private function addExtractorNode(): NodeDefinition
    {
        $builder = new TreeBuilder();
        $root = $builder->root('extractor');
        $root
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifString()
                ->then(function ($extractorName) {
                    return ['service_id' => 'eps.req2cmd.extractor.' . $extractorName];
                })
            ->end()
            ->children()
                ->scalarNode('service_id')
                    ->cannotBeEmpty()
                    ->defaultValue('eps.req2cmd.extractor.serializer')
                ->end()
            ->end();

        return $root;
    }

    private function addCommandBusNode(): NodeDefinition
    {
        $builder = new TreeBuilder();
        $node = $builder->root('command_bus');
        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifString()
                ->then(function (string $svcId) {
                    return ['service_id' => 'eps.req2cmd.command_bus.' . $svcId];
                })
            ->end()
            ->children()
                ->scalarNode('service_id')
                    ->cannotBeEmpty()
                    ->defaultValue('eps.req2cmd.command_bus.tactician')
                ->end()
                ->scalarNode('name')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($config) {
                    return $config['service_id'] !== 'eps.req2cmd.command_bus.tactician';
                })
                ->then(function ($config) {
                    unset($config['name']);
                    return $config;
                })
            ->end();

        return $node;
    }

    private function addEventListenersNode(): NodeDefinition
    {
        $builder = new TreeBuilder();
        $node = $builder->root('listeners');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('extractor')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()
                        ->ifTrue(function ($enabled) { return is_bool($enabled); })
                        ->then(function ($isEnabled) {
                            return ['enabled' => $isEnabled];
                        })
                    ->end()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultValue(true)
                        ->end()
                        ->integerNode('priority')
                            ->defaultValue(0)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
