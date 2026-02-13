<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\Database;

use Continuum\Security\Authorization\Voter\Admin\BackupVoter;
use Continuum\Service\Database\DatabaseDumper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BackupController extends AbstractController
{
    public function __construct(
        private readonly DatabaseDumper $databaseDumper,
    ) {}

    #[Route(path: '/admin/backups', name: 'app_admin_backups', methods: ['GET'])]
    #[IsGranted(BackupVoter::VIEW)]
    public function __invoke(): Response
    {
        $backups = $this->databaseDumper->getBackups();

        return $this->render('admin/backup/index.html.twig', [
            'has_relevant_backup' => $this->databaseDumper->hasRelevantBackup(),
            'backups' => $backups,
        ]);
    }
}
