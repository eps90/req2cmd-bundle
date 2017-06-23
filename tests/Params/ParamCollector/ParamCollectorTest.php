<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Params\ParamCollector;

use Eps\Req2CmdBundle\Params\ParamCollector\ParamCollector;
use Eps\Req2CmdBundle\Params\ParameterMapper\ParamMapperInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ParamCollectorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCollectParamsFromAllMappedMappers(): void
    {
        $firstResult = [
            'id' => 321
        ];
        $firstMapper = $this->createMapper($firstResult);
        $secondResult = [
            'name' => 'My command'
        ];
        $secondMapper = $this->createMapper($secondResult);

        $collector = new ParamCollector([$firstMapper, $secondMapper]);

        $expectedResult = [
            'id' => 321,
            'name' => 'My command'
        ];
        $actualResult = $collector->collect(new Request(), ['some', 'values']);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldOverrideParamsWhenThereIsANameConflict(): void
    {
        $firstResult = [
            'id' => 321
        ];
        $firstMapper = $this->createMapper($firstResult);
        $secondResult = [
            'id' => 456
        ];
        $secondMapper = $this->createMapper($secondResult);

        $collector = new ParamCollector([$firstMapper, $secondMapper]);

        $expectedResult = [
            'id' => 456
        ];
        $actualResult = $collector->collect(new Request(), ['some', 'values']);

        static::assertEquals($expectedResult, $actualResult);
    }

    private function createMapper(array $returnValues): ParamMapperInterface
    {
        return new class($returnValues) implements ParamMapperInterface {
            private $returnValues;

            public function __construct(array $returnValues)
            {
                $this->returnValues = $returnValues;
            }

            public function map(Request $request, array $propsMap): array
            {
                return $this->returnValues;
            }
        };
    }
}
