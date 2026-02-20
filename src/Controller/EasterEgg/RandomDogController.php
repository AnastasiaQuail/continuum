<?php

declare(strict_types=1);

namespace Continuum\Controller\EasterEgg;

use Continuum\Component\Dog\DogClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RandomDogController extends AbstractController
{
    public function __construct(
        private readonly DogClient $dogClient,
    ) {}

    #[Route(path: '/easter-eggs/dogs', name: 'app_easter_egg_dog', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'href' => $this->dogClient->getRandomImage(),
        ]);
    }
}
