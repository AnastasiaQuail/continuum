<?php

declare(strict_types=1);

namespace Continuum\Controller\User;

use Continuum\Entity\User;
use Continuum\Service\UserService;
use DateInvalidTimeZoneException;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class TimezoneUpdateController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Route(path: '/users/timezone', name: 'app_user_timezone_update', methods: ['PATCH'])]
    public function __invoke(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        try {
            $timezone = new DateTimeZone($request->request->getString('timezone'));
        } catch (DateInvalidTimeZoneException $e) {
            throw new BadRequestHttpException('Invalid timezone provided', $e);
        }

        $this->userService->updateTimezone($user, $timezone);

        return $this->json([
            'new_timezone' => $user->getTimezone()->getName(),
        ]);
    }
}
