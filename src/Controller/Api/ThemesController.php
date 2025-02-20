<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ThemesController extends AbstractController
{
    #[Route('/themes', name: 'app_themes', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'themes' => [
                [
                    'code' => 'environment',
                    'id' => 1234,
                    'parentId' => null,
                    'children' => [
                        [
                            'code' => 'some_sub_theme',
                            'id' => 1024,
                            'parentId' => 1234,
                            'children' => [
                                [
                                    'code' => 'yet_another_sub_theme',
                                    'id' => 2048,
                                    'parentId' => 1024,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
