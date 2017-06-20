<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\CommandExtractor;

use Eps\Req2CmdBundle\CommandExtractor\MockCommandExtractor;
use Eps\Req2CmdBundle\Tests\Fixtures\Command\DummyCommand;
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
}
