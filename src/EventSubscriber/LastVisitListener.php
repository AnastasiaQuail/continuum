<?php

declare(strict_types=1);

namespace Continuum\EventSubscriber;

use Continuum\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class LastVisitListener
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
    ) {}

    #[AsEventListener]
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user->getLastVisitedAt()->modify('+5 minutes') < new DateTimeImmutable()) {
            $user->visited();

            $this->entityManager->flush();
        }
    }
}
