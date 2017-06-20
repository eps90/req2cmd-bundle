<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\DependencyInjection;

use Eps\Req2CmdBundle\Action\ApiResponderAction;
use Eps\Req2CmdBundle\DependencyInjection\Req2CmdExtension;
use Eps\Req2CmdBundle\EventListener\ExtractCommandFromRequestListener;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class Req2CmdExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new Req2CmdExtension()];
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
