<?php

declare(strict_types=1);

namespace Continuum\Controller\Admin\Backup;

use Continuum\Security\Authorization\Voter\Admin\BackupVoter;
use Continuum\Service\Database\DatabaseDumper;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DownloadController extends AbstractController
{
    public function __construct(
        private readonly DatabaseDumper $databaseDumper,
    ) {}

    #[Route(path: '/admin/backups/{filename}', name: 'app_admin_backup_download', methods: ['GET'])]
    #[IsGranted(BackupVoter::DOWNLOAD)]
    public function __invoke(string $filename): BinaryFileResponse
    {
        try {
            $backup = $this->databaseDumper->getBackup($filename);
        } catch (RuntimeException $e) {
            throw $this->createNotFoundException('Backup file not found', $e);
        }

        $response = $this->file($backup);
        $response->headers->set('Content-Type', 'application/sql');

        return $response;
    }
}
