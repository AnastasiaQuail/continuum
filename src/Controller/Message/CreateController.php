<?php

declare(strict_types=1);

namespace Continuum\Controller\Message;

use Continuum\Entity\Message;
use Continuum\Entity\User;
use Continuum\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

final class CreateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ChatRepository $chatRepository,
    ) {}

    #[Route(path: '/messages/{chatId}', name: 'app_message_create', methods: ['POST'])]
    public function __invoke(#[CurrentUser] User $user, Request $request, Uuid $chatId): RedirectResponse
    {
        $chat = $this->chatRepository->findOneById($chatId, $user->id);

        if (null === $chat) {
            throw $this->createNotFoundException('The chat was not found.');
        }

        $message = new Message(
            chat: $chat,
            sender: $user,
            content: $request->request->get('content'),
        );

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_messages', ['chatId' => $chat->id]);
    }
}
