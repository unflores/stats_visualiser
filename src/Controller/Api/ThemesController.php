<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ThemeRepository;
#[Route('/api')]
final class ThemesController extends AbstractController
{
    private $themeRepository;

    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    #[Route('/themes', name: 'app_themes', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->themeRepository->findAllHierarchical());
    }
}
