<?php

declare(strict_types=1);

namespace App\Http\Service\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PayloadArgumentResolver implements ArgumentValueResolverInterface
{
    private const ALLOW_SCALAR_TYPES = ['string', 'int', 'float', 'bool'];
    private const ALLOW_COMPOUND_TYPES = ['array'];

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        $instance = null;
        if (\class_exists($className)) {
            try {
                $instance = new $className();
            } catch (\Throwable $exception) {
                return false;
            }
        }

        return $instance instanceof BasePayloadInterface;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable|void
     * @throws \ReflectionException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $classReflection = new \ReflectionClass($argument->getType());
        $queryParams = $request->query->all();

        yield $this->hydratePayload($queryParams, $classReflection);
    }

    /**
     * @throws \ReflectionException
     */
    private function hydratePayload($queryParams, \ReflectionClass $classReflection): object
    {
        $allowTypes = \array_merge(self::ALLOW_SCALAR_TYPES, self::ALLOW_COMPOUND_TYPES);

        if ([] === $queryParams) {
            return $classReflection->newInstance();
        }

        $classReflectionProperties = $classReflection->getProperties();
        $instance = $classReflection->newInstance();

        foreach ($classReflectionProperties as $property) {
            if (false === isset($queryParams[$property->name])) {
                continue;
            }

            $propertyType = $property->getType();
            $property->setAccessible(true);

            if (null === $propertyType) {
                $property->setValue($instance, $queryParams[$property->name]);
            }

            if (false === \in_array($propertyType->getName(), $allowTypes, true)) {
                continue;
            }

            $value = $this->prepareQueryParam($queryParams[$property->name]);

            if (null === $value && false === $propertyType->allowsNull()) {
                continue;
            }

            if (true === \in_array($propertyType->getName(), self::ALLOW_COMPOUND_TYPES)) {
                $property->setValue($instance, $this->castToCompoundType($value));
                continue;
            }

            $property->setValue($instance, $this->castToScalarType($value, $propertyType->getName()));
        }

        return $instance;
    }

    private function prepareQueryParam($paramValue)
    {
        $value = null;
        $paramValue = \is_string($paramValue) ? \trim($paramValue) : $paramValue;

        switch ($paramValue) {
            case 'null';
                break;
            case 'true';
                $value = true;
                break;
            case 'false';
                $value = false;
                break;
            default:
                $value = $paramValue;
        }

        return $value;
    }

    private function castToScalarType($value, string $type)
    {
        if (false === \in_array(\gettype($value), self::ALLOW_SCALAR_TYPES)) {
            throw new \InvalidArgumentException('Request parameter definition error.');
        }

        \settype($value, $type);
        return $value;
    }

    private function castToCompoundType($value): array
    {
        if (false === \in_array(\gettype($value), self::ALLOW_COMPOUND_TYPES)) {
            throw new \InvalidArgumentException('Request parameter definition error.');
        }

        return \array_map(function ($paramValue) {
            return $this->prepareQueryParam($paramValue);
        }, $value);
    }
}