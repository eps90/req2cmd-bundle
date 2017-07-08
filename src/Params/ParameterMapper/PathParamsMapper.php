<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Params\ParameterMapper;

use Eps\Req2CmdBundle\Exception\ParamMapperException;
use Symfony\Component\HttpFoundation\Request;

class PathParamsMapper implements ParamMapperInterface
{
    private const REQUIRED_PROPERTY_PREFIX = '!';

    public function map(Request $request, array $propsMap): array
    {
        if (!array_key_exists('path', $propsMap)) {
            return [];
        }

        $pathProps = (array)$propsMap['path'];
        $result = [];
        foreach ($pathProps as $paramName => $paramValue) {
            if ($required = $this->isParamRequired($paramName)) {
                $paramName = substr($paramName, 1);
                $this->assertRequiredParamIsPresent($paramName, $request);
            }

            if (!$request->attributes->has($paramName)) {
                continue;
            }

            $finalPropName = $paramValue ?? $paramName;
            $result[$finalPropName] = $request->attributes->get($paramName);
        }

        return $result;
    }

    private function isParamRequired(string $paramName): bool
    {
        return strpos($paramName, self::REQUIRED_PROPERTY_PREFIX) === 0;
    }

    private function assertRequiredParamIsPresent(string $paramName, Request $request): void
    {
        if (!$request->attributes->has($paramName)) {
            throw ParamMapperException::noParamFound($paramName);
        }

        if ($request->attributes->get($paramName) === null) {
            throw ParamMapperException::paramEmpty($paramName);
        }
    }
}
