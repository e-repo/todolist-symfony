<?php

declare(strict_types=1);

namespace App\Domain\Auth\UseCase\Email\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $id;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $newEmail;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $token;
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $confirmUrl;

    /**
     * @param string $id
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $newEmail
     * @return self
     */
    public function setNewEmail(string $newEmail): self
    {
        $this->newEmail = $newEmail;
        return $this;
    }

    /**
     * @param string $token
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param string $confirmUrl
     * @return self
     */
    public function setConfirmUrl(string $confirmUrl): self
    {
        $this->confirmUrl = $confirmUrl;
        return $this;
    }
}