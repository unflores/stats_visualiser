<?php

namespace App\Import;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;

class SaveTheme
{
    private $entityManager;
    private $themeRepository;

    // ajoute apres les test
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->themeRepository = $entityManager->getRepository(Theme::class);
    }

    public function saveOnDatabase(?string $filePath = null): mixed
    {
        $file = $filePath ?? '/public/File/themes.json';
        if (!file_exists($file)) {
            return ['File not found'];
        }
        $data = json_decode(file_get_contents($file), true);

        foreach ($data as $theme) {
            $this->entityManager->persist(
                (new Theme())
                    ->setCode($theme['code'])
                    ->setId($theme['id'])
                    ->setParentId($theme['parentId'])
                    ->setExternalId($theme['externalId'])
                    ->setIsSection($theme['isSection'])
            );
        }
        $this->entityManager->flush();
        $themes = $this->themeRepository->findAll();

        return count($themes) > 0 ? true : false;
    }

    public function saveDatabase(?string $filePath = null): mixed
    {
        $file = $filePath ?? '/public/File/themes.json';
        if (!file_exists($file)) {
            return ['File not found'];
        }
        $data = json_decode(file_get_contents($file), true);

        return $data;
    }
}
