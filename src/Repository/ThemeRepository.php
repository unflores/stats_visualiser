<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    public function findAllHierarchical(): array
    {
        $themes = $this->findAll();
        $themesByParentId = [];
        array_map(function (Theme $theme) use (&$themesByParentId) {
            $children = $themesByParentId[$theme->getParentId()] ?? [];
            $children[] = ['id' => $theme->getId(), 'name' => $theme->getName(), 'parentId' => $theme->getParentId(), 'externalId' => $theme->getExternalId()];
            $themesByParentId[$theme->getParentId() ?? 'base'] = $children;
        }, $themes);

        return $themesByParentId;
    }

    public function checkThemesExiste(array $themes): bool
    {
        $sizeThemes = count($themes);
        $themesCount = count($this->findAll());
        if ($sizeThemes === $themesCount) {
            return true;
        } 
        return false;
    }

    public function saveThemes(array $themes):bool
    {
        return false;   
    }
}
