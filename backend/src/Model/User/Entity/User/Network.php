<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Network
 * @package App\Model\User\Entity\User
 * @ORM\Entity()
 * @ORM\Table(
 *      name="user_user_networks",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"network", "identity"})}
 * )
 */
class Network
{
    /**
     * Сущность простая, не будем создавать
     * отдельный объект Id
     *
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id()
     */
    private string $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="networks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;
    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private string $network;
    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private string $identity;

    public function __construct(User $user, string $network, string $identity)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }
    /**
     * @param string $network
     * @return bool
     */
    public function isNetworkAttached(string $network): bool
    {
        return $this->network === $network;
    }
    /**
     * @return string
     */
    public function getNetwork(): string
    {
        return $this->network;
    }
    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function changeUser(User $user): void
    {
        if ($this->user === $user) {
            throw new \DomainException('User is already changed.');
        }

        $this->user = $user;
    }
}