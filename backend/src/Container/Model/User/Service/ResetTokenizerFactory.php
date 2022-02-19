<?php

declare(strict_types=1);

namespace App\Container\Model\User\Service;

use App\Model\User\Service\ResetTokenizer;

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