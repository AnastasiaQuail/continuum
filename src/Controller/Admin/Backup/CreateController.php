<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\Backup;

use Continuum\Security\Authorization\Voter\Admin\BackupVoter;
use Continuum\Service\Database\DatabaseDumper;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateController extends AbstractController
{
    public function __construct(
        private readonly DatabaseDumper $databaseDumper,
    ) {}

    #[Route(path: '/admin/backups', name: 'app_admin_backup_create', methods: ['POST'])]
    #[IsGranted(BackupVoter::CREATE)]
    public function __invoke(): RedirectResponse
    {
        try {
            $backupPath = $this->databaseDumper->makeBackup();
        } catch (RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('app_admin_backups');
        }

        /** @var int<0, max> $filesize */
        $filesize = filesize($backupPath);
        $this->addFlash(
            'success',
            sprintf(
                'Backup created: %s (%s MB)',
                array_last(explode('/', $backupPath)),
                number_format($filesize / 1024 / 1024, 2)
            )
        );

        return $this->redirectToRoute('app_admin_backups');
    }
}
