<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin;

use Continuum\Service\DatabaseDumpCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly DatabaseDumpCache $databaseDumpCache,
    ) {}

    #[Route(path: '/admin', name: 'app_admin_homepage')]
    public function __invoke(): Response
    {
        return $this->render('admin/default/homepage.html.twig', [
            'hasDBBackup' => $this->databaseDumpCache->has(),
        ]);
    }
}
