<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Service;

use Ramsey\Uuid\Uuid;

class SignUpConfirmTokenizer
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}