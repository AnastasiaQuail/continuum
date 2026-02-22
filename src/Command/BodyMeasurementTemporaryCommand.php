<?php

declare(strict_types=1);

namespace Continuum\Command;

use Continuum\Entity\BodyMeasurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:temp:body-measurement',
    description: 'Migrate int values to float',
)]
final readonly class BodyMeasurementTemporaryCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        /** @var list<BodyMeasurement> $entities */
        $entities = $this->entityManager->getRepository(BodyMeasurement::class)->findAll();

        foreach ($entities as $entity) {
            $entity->fatDeurenberg = $this->from($entity->fatDeurenberg, 100, 2);
            $entity->fatUsNavy = $this->from($entity->fatUsNavy, 100, 2);
            $entity->weight = $this->from($entity->weight, 1000);
            $entity->neck = $this->from($entity->neck);
            $entity->chest = $this->from($entity->chest);
            $entity->shoulders = $this->from($entity->shoulders);
            $entity->waist = $this->from($entity->waist);
            $entity->flexedBiceps = $this->from($entity->flexedBiceps);
            $entity->hips = $this->from($entity->hips);
            $entity->thigh = $this->from($entity->thigh);
            $entity->calf = $this->from($entity->calf);
        }

        $this->entityManager->flush();

        $io->success('Migration completed successfully.');

        return Command::SUCCESS;
    }

    /**
     * @template T of float|int|null
     *
     * @param T $value
     *
     * @return (T is null ? null : float)
     */
    private function from(float|int|null $value, int $coefficient = 10, int $precision = 1): ?float
    {
        return null !== $value ? round($value / $coefficient, $precision) : null;
    }
}
