<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\ParameterMapper;

use Eps\Req2CmdBundle\Exception\ParamMapperException;
use Eps\Req2CmdBundle\Params\ParameterMapper\PathParamsMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PathParamsMapperTest extends TestCase
{
    /**
     * @var PathParamsMapper
     */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new PathParamsMapper();
    }

    /**
     * @test
     */
    public function itShouldExtractParametersFromRequest(): void
    {
        $requestParams = [
            'id' => 321
        ];
        $request = new Request([], [], $requestParams);
        $props = [
            'path' => [
                'id' => null
            ]
        ];

        $expectedResult = ['id' => 321];
        $actualResult = $this->mapper->map($request, $props);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToSetParameterName(): void
    {
        $requestParams = [
            'id' => 321
        ];
        $request = new Request([], [], $requestParams);
        $props = [
            'path' => [
                'id' => 'custom_id'
            ]
        ];
        $expectedResult = [
            'custom_id' => 321
        ];
        $actualResult = $this->mapper->map($request, $props);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldReturnAnEmptyArrayWhenPathKeyDoesNotExist(): void
    {
        $requestParams = [
            'id' => 321
        ];
        $request = new Request([], [], $requestParams);
        $props = [];

        $expectedResult = [];
        $actualResult = $this->mapper->map($request, $props);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldDoNothingPropertyDoesNotExist(): void
    {
        $requestParams = [
            'id' => 321
        ];
        $request = new Request([], [], $requestParams);
        $props = [
            'path' => [
                'missing_property' => null
            ]
        ];

        $expectedResult = [];
        $actualResult = $this->mapper->map($request, $props);

        static::assertEquals($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function itShouldThrowWhenRequiredPropertyDoesNotExistInRequest(): void
    {
        $this->expectException(ParamMapperException::class);

        $requestParams = [
            'id' => 321
        ];
        $request = new Request([], [], $requestParams);
        $props = [
            'path' => [
                '!missing_property' => null
            ]
        ];

        $this->mapper->map($request, $props);
    }

    /**
     * @test
     */
    public function itShouldThrowWhenRequiredPropertyIsNull(): void
    {
        $this->expectException(ParamMapperException::class);

        $requestParams = [
            'id' => null
        ];
        $request = new Request([], [], $requestParams);
        $props = [
            'path' => [
                '!id' => null
            ]
        ];

        $this->mapper->map($request, $props);
    }
}
