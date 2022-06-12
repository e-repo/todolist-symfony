<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

use App\Http\Service\JsonApi\ResponseBuilder\GetterNotFoundException;

abstract class AbstractParams
{
    protected const ALL_PARAMS_PROPERTY = 'allParams';

    abstract protected function getProperties(): array;

    /**
     * @return array
     * @throws GetterNotFoundException
     */
    public function createProperties(): array
    {
        $paramsInstance = new static();
        $paramsClassName = (new \ReflectionClass($paramsInstance))->getShortName();
        $partsClassName = \array_filter(
            \preg_split('/(?=[A-Z])/', $paramsClassName)
        );
        $firstPartClassName = \array_shift($partsClassName);

        $properties = [];
        foreach ($this->getProperties() as $property) {

            $getterName = \sprintf('get' . \ucfirst($firstPartClassName) . '%s', \ucfirst($property));

            if (! \method_exists($this, $getterName)) {
                throw new GetterNotFoundException($getterName);
            }

            if ((self::ALL_PARAMS_PROPERTY === $property) && null !== $this->$getterName()) {
                return $this->$getterName();
            }

            if ($value = $this->$getterName()) {
                $properties[$property] = $value;
            }
        }

        return [
            $properties
        ];
    }
}