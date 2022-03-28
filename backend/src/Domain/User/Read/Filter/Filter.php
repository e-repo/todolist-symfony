<?php

declare(strict_types=1);

namespace App\Domain\User\Read\Filter;


class Filter
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;
}