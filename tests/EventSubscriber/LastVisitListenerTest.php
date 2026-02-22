<?php

declare(strict_types=1);

namespace Continuum\Tests\EventSubscriber;

use Continuum\Entity\User;
use Continuum\EventSubscriber\LastVisitListener;
use Continuum\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(LastVisitListener::class)]
final class LastVisitListenerTest extends KernelTestCase
{
    private MockObject&UserRepositoryInterface $userRepository;
    private LastVisitListener $listener;
    private RequestEvent $event;

    #[Override]
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->listener = new LastVisitListener(
            security: self::getContainer()->get(Security::class),
            userRepository: $this->userRepository = $this->createMock(UserRepositoryInterface::class)
        );

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $this->event = new RequestEvent(
            kernel: $kernel,
            request: $request,
            requestType: HttpKernelInterface::MAIN_REQUEST,
        );
    }

    public function testInvokeNotMainRequest(): void
    {
        $subRequestEvent = new RequestEvent(
            kernel: $this->event->getKernel(),
            request: $this->event->getRequest(),
            requestType: HttpKernelInterface::SUB_REQUEST,
        );
        $this->buildUser(new DateTimeImmutable('-10 minutes'));
        $this->userRepository->expects($this->never())->method('updateLastVisitedAt');

        $this->listener->__invoke($subRequestEvent);
    }

    public function testInvokeNotAuthenticated(): void
    {
        $this->userRepository->expects($this->never())->method('updateLastVisitedAt');

        $this->listener->__invoke($this->event);
    }

    public function testInvokeLessFiveMinutes(): void
    {
        $this->buildUser();
        $this->userRepository->expects($this->never())->method('updateLastVisitedAt');

        $this->listener->__invoke($this->event);
    }

    public function testInvokeUpdateLastVisited(): void
    {
        $user = $this->buildUser(new DateTimeImmutable('-5 minutes'));
        $this->userRepository->expects($this->once())->method('updateLastVisitedAt')->with($user->id);

        $this->listener->__invoke($this->event);

        $session = $this->event->getRequest()->getSession();
        self::assertInstanceOf(FlashBagAwareSessionInterface::class, $session);
        self::assertSame([], $session->getFlashBag()->get('success'));
    }

    public function testInvokeUpdateLastVisitedAndPushFlashMessage(): void
    {
        $user = $this->buildUser(new DateTimeImmutable('-8 hours'));
        $this->userRepository->expects($this->once())->method('updateLastVisitedAt')->with($user->id);

        $this->listener->__invoke($this->event);

        $session = $this->event->getRequest()->getSession();
        self::assertInstanceOf(FlashBagAwareSessionInterface::class, $session);
        self::assertSame(["Welcome back! It's good to see you again"], $session->getFlashBag()->get('success'));
    }

    private function buildUser(?DateTimeImmutable $lastVisitedAt = null): User
    {
        $user = new User('email@example.com');
        $user->password = 'password';

        if (null !== $lastVisitedAt) {
            new ReflectionProperty(User::class, 'lastVisitedAt')
                ->setValue($user, $lastVisitedAt);
        }

        self::getContainer()
            ->get('security.untracked_token_storage')
            ->setToken(new TestBrowserToken($user->getRoles(), $user));

        return $user;
    }
}
