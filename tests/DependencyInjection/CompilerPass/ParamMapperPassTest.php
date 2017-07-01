<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\DependencyInjection\CompilerPass;

use Eps\Req2CmdBundle\DependencyInjection\CompilerPass\ParamMapperPass;
use Eps\Req2CmdBundle\Params\ParamCollector\ParamCollectorInterface;
use Eps\Req2CmdBundle\Params\ParameterMapper\ParamMapperInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParamMapperPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ParamMapperPass());
    }

    /**
     * @test
     */
    public function itShouldCollectParamMappersAndInjectItToTheCollector(): void
    {
        $collectorDefinition = new Definition(ParamCollectorInterface::class);
        $collectorDefinition->setArguments([[]]);
        $this->setDefinition(ParamMapperPass::COLLECTOR_SVC_ID, $collectorDefinition);

        $firstMapperDef = new Definition(ParamMapperInterface::class);
        $firstMapperDef->addTag(ParamMapperPass::MAPPER_TAG);
        $this->setDefinition('first_mapper', $firstMapperDef);

        $secondMapperDef = new Definition(ParamMapperInterface::class);
        $secondMapperDef->addTag(ParamMapperPass::MAPPER_TAG);
        $this->setDefinition('second_mapper', $secondMapperDef);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            ParamMapperPass::COLLECTOR_SVC_ID,
            0,
            [
                new Reference('first_mapper'),
                new Reference('second_mapper')
            ]
        );
    }

    /**
     * @test
     */
    public function itShouldSortMappersByPriority(): void
    {
        $collectorDefinition = new Definition(ParamCollectorInterface::class);
        $collectorDefinition->setArguments([[]]);
        $this->setDefinition(ParamMapperPass::COLLECTOR_SVC_ID, $collectorDefinition);

        $firstMapperDef = new Definition(ParamMapperInterface::class);
        $firstMapperDef->addTag(ParamMapperPass::MAPPER_TAG, ['priority' => 1]);
        $this->setDefinition('first_mapper', $firstMapperDef);

        $secondMapperDef = new Definition(ParamMapperInterface::class);
        $secondMapperDef->addTag(ParamMapperPass::MAPPER_TAG, ['priority' => 2]);
        $this->setDefinition('second_mapper', $secondMapperDef);

        $this->compile();

        $actualDefinition = $this->container->findDefinition(ParamMapperPass::COLLECTOR_SVC_ID);
        $actualArgs = $actualDefinition->getArgument(0);
        $expectedArgs = [new Reference('second_mapper'), new Reference('first_mapper')];

        static::assertEquals($expectedArgs, $actualArgs);
    }
}
