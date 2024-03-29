<?php

declare(strict_types=1);

namespace App\Domain\Service;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * Flusher constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function flush(): void
    {
        $this->em->flush();
    }

    public function persist(object $entity): void
    {
        $this->em->persist($entity);
    }
}
