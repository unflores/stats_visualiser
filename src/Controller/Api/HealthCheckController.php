<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthCheckController extends AbstractController
{
    #[Route('/healthcheck', name: 'healthcheck')]
    public function index(): JsonResponse
    {
        return $this->json([
            'health' => 'Lookin good'
        ]);
    }
}
