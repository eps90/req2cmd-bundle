<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
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
                ->arrayNode('extractor')
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
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
