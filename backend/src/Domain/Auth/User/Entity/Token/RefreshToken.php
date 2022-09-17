<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\Entity\Token;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * @ORM\Entity
 * @ORM\Table("refresh_tokens")
 */
class RefreshToken extends BaseRefreshToken
{
}