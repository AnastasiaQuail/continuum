<?php

declare(strict_types=1);

namespace Continuum\EventSubscriber;

use Continuum\Entity\User;
use Continuum\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class LastVisitListener
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
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

        $now = new DateTimeImmutable();
        $lastVisited = $user->getLastVisitedAt();

        if ($lastVisited->modify('+5 minutes') < $now) {
            $this->userRepository->updateLastVisitedAt($user);

            if ($lastVisited->modify('+8 hours') < $now) {
                /** @var FlashBagAwareSessionInterface $session */
                $session = $event->getRequest()->getSession();
                $session->getFlashBag()->add('success', 'Welcome back! It\'s good to see you again');
            }
        }
    }
}
