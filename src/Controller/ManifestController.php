<?php

declare(strict_types=1);

namespace Continuum\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ManifestController extends AbstractController
{
    public function __construct(
        #[Autowire(param: 'app.title')]
        private readonly string $title,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Packages $packages,
    ) {}

    #[Route(path: '/manifest.webmanifest', name: 'app_manifest', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'name' => $this->title,
            'short_name' => $this->title,
            'start_url' => $this->urlGenerator->generate('app_homepage'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#ffffff',
            'icons' => [
                [
                    'src' => $this->packages->getUrl('favicon.svg'),
                    'sizes' => 'any',
                    'type' => 'image/svg',
                ],
            ],
        ]);
    }
}
