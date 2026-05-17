<?php

declare(strict_types=1);

namespace Continuum\Tests\EventSubscriber;

use Continuum\EventSubscriber\LastVisitListener;
use Override;
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[CoversNothing]
final class EventSubscribersTest extends KernelTestCase
{
    private EventDispatcherInterface $eventDispatcher;

    #[Override]
    protected function setUp(): void
    {
        $this->eventDispatcher = self::getContainer()->get('event_dispatcher');
    }

    public function testAppSubscribers(): void
    {
        $eventListeners = [];

        foreach ($this->eventDispatcher->getListeners() as $listeners) {
            /**
             * @var list<array{0: object, 1: non-empty-string}> $listeners
             *
             * @phpstan-ignore varTag.type (fix incorrect symfony contract)
             */
            foreach ($listeners as $listener) {
                $listenerClass = $listener[0]::class;

                if (str_starts_with($listenerClass, 'Symfony')) {
                    continue;
                }

                if (str_starts_with($listenerClass, 'Doctrine')) {
                    continue;
                }

                $eventListeners[] = [$listenerClass, $listener[1]];
            }
        }

        self::assertSame(
            [
                [LastVisitListener::class, '__invoke'],
            ],
            $eventListeners
        );
    }

    public function testFrameworkSubscribers(): void
    {
        self::assertCount(12, $this->eventDispatcher->getListeners(KernelEvents::REQUEST));
        self::assertCount(4, $this->eventDispatcher->getListeners(KernelEvents::EXCEPTION));
        self::assertCount(2, $this->eventDispatcher->getListeners(KernelEvents::CONTROLLER));
        self::assertCount(6, $this->eventDispatcher->getListeners(KernelEvents::CONTROLLER_ARGUMENTS));
        self::assertCount(1, $this->eventDispatcher->getListeners(KernelEvents::VIEW));
        self::assertCount(11, $this->eventDispatcher->getListeners(KernelEvents::RESPONSE));
        self::assertCount(4, $this->eventDispatcher->getListeners(KernelEvents::FINISH_REQUEST));
        self::assertCount(1, $this->eventDispatcher->getListeners(KernelEvents::TERMINATE));

        self::assertCount(3, $this->eventDispatcher->getListeners(ConsoleEvents::COMMAND));
        self::assertCount(0, $this->eventDispatcher->getListeners(ConsoleEvents::SIGNAL));
        self::assertCount(2, $this->eventDispatcher->getListeners(ConsoleEvents::TERMINATE));
        self::assertCount(3, $this->eventDispatcher->getListeners(ConsoleEvents::ERROR));

        self::assertCount(3, $this->eventDispatcher->getListeners(CheckPassportEvent::class));
        self::assertCount(1, $this->eventDispatcher->getListeners(LoginSuccessEvent::class));
        self::assertCount(1, $this->eventDispatcher->getListeners(LogoutEvent::class));
    }
}
