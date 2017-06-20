<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\Tests\DependencyInjection;

use Eps\Request2CommandBusBundle\Action\ApiResponderAction;
use Eps\Request2CommandBusBundle\DependencyInjection\Request2CommandBusExtension;
use Eps\Request2CommandBusBundle\EventListener\ExtractCommandFromRequestListener;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class Request2CommandBusExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new Request2CommandBusExtension()];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    /**
     * @test
     */
    public function itShouldLoadExtractorsDefinitions(): void
    {
        $this->assertContainerBuilderHasService('eps.req2cmd.extractor.serializer');
        $this->assertContainerBuilderHasAlias('eps.req2cmd.extractor', 'eps.req2cmd.extractor.serializer');
    }

    /**
     * @test
     */
    public function itShouldHaveActionsDefinitions(): void
    {
        $this->assertContainerBuilderHasService('eps.req2cmd.action.api_responder', ApiResponderAction::class);
    }

    /**
     * @test
     */
    public function itShouldHaveListenersDefinitions(): void
    {
        $this->assertContainerBuilderHasService(
            'eps.req2cmd.listener.extract_command',
            ExtractCommandFromRequestListener::class
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'eps.req2cmd.listener.extract_command',
            'kernel.event_listener',
            [
                'method' => 'onKernelRequest',
                'event' => 'kernel.request'
            ]
        );
    }
}
