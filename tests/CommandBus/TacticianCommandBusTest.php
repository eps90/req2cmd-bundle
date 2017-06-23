<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\CommandBus;

use Eps\Req2CmdBundle\CommandBus\TacticianCommandBus;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyCommand;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;

class TacticianCommandBusTest extends TestCase
{
    /**
     * @var CommandBus|\PHPUnit_Framework_MockObject_MockObject
     */
    private $deferredCB;

    /**
     * @var TacticianCommandBus
     */
    private $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deferredCB = $this->createMock(CommandBus::class);
        $this->commandBus = new TacticianCommandBus($this->deferredCB);
    }

    /**
     * @test
     */
    public function itShouldPassACommandToTacticianCommandBus(): void
    {
        $command = new DummyCommand('a name', ['opts']);
        $this->deferredCB->expects(static::once())
            ->method('handle')
            ->with($command);

        $this->commandBus->handleCommand($command);
    }
}
