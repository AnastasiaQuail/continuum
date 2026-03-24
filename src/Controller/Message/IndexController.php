<?php

declare(strict_types=1);

namespace Continuum\Controller\Message;

use Continuum\Entity\Chat;
use Continuum\Entity\User;
use Continuum\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ChatRepository $chatRepository,
    ) {}

    #[Route(path: '/messages/{chatId}', name: 'app_messages', methods: ['GET'])]
    public function __invoke(#[CurrentUser] User $user, ?Uuid $chatId = null): Response
    {
        if (null === $chatId) {
            $chat = $this->chatRepository->findLastOneByUserId($user->id);
        } else {
            $chat = $this->chatRepository->findOneById($chatId, $user->id);
        }

        if (null === $chat) {
            $otherUser = $this->entityManager->find(
                User::class,
                UuidV7::fromString('019c1162-eebc-7805-b63e-0eeebfb6f4b6')
            );

            $chat = new Chat($user, $otherUser);

            $this->entityManager->persist($chat);
            $this->entityManager->flush();
        }

        return $this->render('message/index.html.twig', [
            'chat' => $chat,
            'messages' => $chat->messages,
        ]);
    }
}
