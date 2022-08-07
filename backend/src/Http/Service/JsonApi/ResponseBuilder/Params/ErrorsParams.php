<?php

declare(strict_types=1);

namespace App\Http\Service\JsonApi\ResponseBuilder\Params;

use App\Http\Service\JsonApi\ResponseBuilder\GetterNotFoundException;

class ErrorsParams extends AbstractParams
{
    private ?array $errorDetail = null;
    private ?array $errorStatus = null;
    private ?array $errorTitle = null;

    private array $errorProperties = [
        'status',
        'detail',
        'title',
    ];

    public function getErrorsDetail(): ?array
    {
        return $this->errorDetail;
    }

    public function setErrorsDetail(?string $errorDetail): self
    {
        $this->errorDetail[] = $errorDetail;
        return $this;
    }

    public function getErrorsStatus(): ?array
    {
        return $this->errorStatus;
    }

    public function setErrorsStatus(?int $errorStatus): self
    {
        $this->errorStatus[] = $errorStatus;
        return $this;
    }

    public function getErrorsTitle(): ?array
    {
        return $this->errorTitle;
    }

    public function setErrorsTitle(?string $errorTitle): self
    {
        $this->errorTitle[] = $errorTitle;
        return $this;
    }

    protected function getProperties(): array
    {
        return $this->errorProperties;
    }

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

            $value = $this->$getterName();

            if (true === empty($value)) {
                continue;
            }

            if (\is_array($value)) {

                if ([] === $properties) {
                    $properties = \array_map(static function ($valueItem) use ($property) {
                        return [$property => $valueItem];
                    }, $value);

                    continue;
                }

                foreach ($value as $key => $valueItem) {
                    $properties[$key][$property] = $valueItem;
                }

                continue;
            }

            $properties[$property] = $value;
        }

        return [
            $properties
        ];
    }
}