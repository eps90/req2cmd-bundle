<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ParamMapperPass implements CompilerPassInterface
{
    public const COLLECTOR_SVC_ID = 'eps.req2cmd.collector.param_collector';
    public const MAPPER_TAG = 'req2cmd.param_mapper';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::COLLECTOR_SVC_ID)) {
            return;
        }

        $collector = $container->findDefinition(self::COLLECTOR_SVC_ID);
        $mappersDefinitions = $container->findTaggedServiceIds(self::MAPPER_TAG);
        $mappers = $this->collectMappers($mappersDefinitions);

        $collector->replaceArgument(0, $mappers);
    }

    private function collectMappers(array $mappersDefinitions): array
    {
        $queue = new \SplPriorityQueue();

        foreach ($mappersDefinitions as $mapperId => $mapperTags) {
            $tagAttributes = $mapperTags[0];
            $priority = $tagAttributes['priority'] ?? 0;
            $queue->insert($mapperId, $priority);
        }

        return array_map(
            function ($mapperId) {
                return new Reference($mapperId);
            },
            iterator_to_array($queue, false)
        );
    }
}
