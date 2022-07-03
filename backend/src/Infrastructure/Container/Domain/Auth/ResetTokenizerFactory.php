<?php

declare(strict_types=1);

namespace App\Infrastructure\Container\Domain\Auth;

use App\Domain\Auth\Service\ResetTokenizer;

class ResetTokenizerFactory
{
    /**
     * Выдается на один час
     * @param string $duration
     *
     * @return ResetTokenizer
     * @throws \Exception
     */
    public function create(string $duration): ResetTokenizer
    {
        return new ResetTokenizer(new \DateInterval($duration));
    }
}