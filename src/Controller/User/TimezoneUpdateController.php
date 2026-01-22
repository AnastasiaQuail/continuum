<?php

declare(strict_types=1);

namespace Continuum\Controller\User;

use Continuum\Entity\User;
use DateInvalidTimeZoneException;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class TimezoneUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(path: '/users/timezone', name: 'app_user_timezone_update', methods: ['PATCH'])]
    public function __invoke(#[CurrentUser] User $user, Request $request): Response
    {
        try {
            $timezone = new DateTimeZone($request->request->getString('timezone'));
        } catch (DateInvalidTimeZoneException $e) {
            throw new BadRequestHttpException('Invalid timezone provided', $e);
        }

        $user->setTimezone($timezone);

        $this->entityManager->flush();

        return $this->json([
            'new_timezone' => $user->getTimezone()->getName(),
        ]);
    }
}
