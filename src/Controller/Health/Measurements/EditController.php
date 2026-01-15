<?php

declare(strict_types=1);

namespace Continuum\Controller\Health\Measurements;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditController extends AbstractController
{
    #[Route(path: '/health/measurements', name: 'app_health_measurements', methods: ['GET'])]
    public function __invoke(): Response
    {
//        $dto = new EditMeasurement(
//            weight: 0,
//            neck: 30,
//            waist: null,
//        );

        /**
         * - id
         * - datetime
         *
         * - age
         * - height
         * - weight
         *
         * - fat_us_navy
         * - fat_deurenberg
         *
         * - neck (шея)
         * - chest (грудь)
         * - shoulders (Плечи - дельты, обхват)
         * - waist (талия)
         * - flexed_biceps (согнутый бицепс)
         * - hips (бёдра / таз)
         * - thigh (верхняя часть бедра)
         * - calf (икра)
         */


        $age = 33;
        $height = 184;
        $weight = 83;
        $neck = 40;
        $waist = 91;

        $fatUSNavy = 495 / (1.0324 - 0.19077 * log10($waist - $neck) + 0.15456 * log10($height)) - 450;

        $bmi = $weight / (($height / 100) ** 2);
        $fatDeurenberg = 1.2 * $bmi + 0.23 * $age - 16.2;

        return $this->render('health/measurements/edit.html.twig', [
            'fat_us_navy' => round($fatUSNavy, 2),
            'fat_deurenberg' => round($fatDeurenberg, 2),
        ]);
    }
}
