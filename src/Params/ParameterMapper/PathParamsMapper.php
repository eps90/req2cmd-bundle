<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Params\ParameterMapper;

use Eps\Req2CmdBundle\Exception\ParamMapperException;
use Symfony\Component\HttpFoundation\Request;

class PathParamsMapper implements ParamMapperInterface
{
    private const REQ_CHAR = '!';

    public function map(Request $request, array $propsMap): array
    {
        if (!array_key_exists('path', $propsMap)) {
            return [];
        }

        $pathProps = (array)$propsMap['path'];
        $result = [];
        foreach ($pathProps as $propName => $propValue) {
            $required = false;
            if (strpos($propName, self::REQ_CHAR) === 0) {
                $required = true;
                $propName = ltrim($propName, self::REQ_CHAR);
            }

            if ($required && $request->attributes->has($propName) && empty($request->attributes->get($propName))) {
                throw ParamMapperException::paramEmpty($propName);
            }

            if (!$request->attributes->has($propName)) {
                if ($required && $request->attributes->get($propName) === null) {
                    throw ParamMapperException::noParamFound($propName);
                }

                continue;
            }

            $finalPropName = $propValue ?? $propName;
            $result[$finalPropName] = $request->attributes->get($propName);
        }

        return $result;
    }
}
