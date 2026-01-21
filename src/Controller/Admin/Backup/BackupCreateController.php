<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\Backup;

use Continuum\Service\DatabaseDumper;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackupCreateController extends AbstractController
{
    public function __construct(
        private readonly DatabaseDumper $databaseDumper,
    ) {}

    #[Route(path: '/admin/database/backup', name: 'app_admin_database_backup_create', methods: ['POST'])]
    public function __invoke(): Response
    {
        try {
            $backupPath = $this->databaseDumper->makeBackup();
        } catch (RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('app_admin_homepage');
        }

        $this->addFlash(
            'success',
            sprintf(
                'Backup created: %s (%s MB)',
                array_last(explode('/', $backupPath)),
                number_format(filesize($backupPath) / 1024 / 1024, 2)
            )
        );

        return $this->redirectToRoute('app_admin_homepage');
    }
}
