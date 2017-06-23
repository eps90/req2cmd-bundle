<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\CommandExtractor;

use Eps\Req2CmdBundle\CommandExtractor\MockCommandExtractor;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyCommand;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyComplexCommand;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class MockCommandExtractorTest extends TestCase
{
    /**
     * @var MockCommandExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new MockCommandExtractor();
    }

    /**
     * @test
     */
    public function itShouldReturnPreviouslySetCommandToReturn(): void
    {
        $someCommand = new DummyCommand('my command', []);
        $this->extractor->willReturn($someCommand);
        $request = new Request();

        $expectedResult = $someCommand;
        $actualResult = $this->extractor->extractFromRequest($request, DummyCommand::class);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToDetermineWhatArgumentsCommandShouldRespondFor(): void
    {
        $firstRequest = new Request();
        $firstClass = DummyCommand::class;
        $firstProps = ['a' => 1, 'b' => 2];
        $firstCommand = new DummyCommand('my_command', []);

        $secondRequest = new Request();
        $secondClass = DummyComplexCommand::class;
        $secondProps = ['b' => 2];
        $secondCommand = new DummyComplexCommand(new DummyId(1), 'a', new \DateTime('2015-01-01'));

        $this->extractor
            ->forArguments($firstRequest, $firstClass, $firstProps)
            ->willReturn($firstCommand)
            ->andThen()
            ->forArguments($secondRequest, $secondClass, $secondProps)
            ->willReturn($secondCommand);

        $expectedResult = $firstCommand;
        $actualResult = $this->extractor->extractFromRequest($firstRequest, $firstClass, $firstProps);

        static::assertEquals($expectedResult, $actualResult);
    }
}
