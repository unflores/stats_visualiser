<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ThemeRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    public function getIdByExternalId(?string $externalId): ?int
    {
        $result = $this
        ->createQueryBuilder('t')
        ->select('t.id')
        ->where('t.externalId = :externalId')
        ->setParameter('externalId', $externalId)
        ->getQuery()
        ->getOneOrNullResult();

        return $result['id'] ?? null;
    }

    public function isFirstThemeParentIdNull(): bool
    {
        $firstTheme = $this->createQueryBuilder('theme')
        ->select('theme.parentId')
        ->orderBy('theme.id', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

        return null !== $firstTheme && null === $firstTheme['parentId'];
    }

    public function isAllThemesParentIdAreNotNull(): bool
    {
        $isAllThemesParentId = true;

        $parentIds = $this->createQueryBuilder('theme')
            ->select('theme.parentId')
            ->orderBy('theme.id', 'ASC')
            ->setFirstResult(1)
            ->getQuery()
            ->getResult();

        foreach ($parentIds as $parentId) {
            if (null === $parentId['parentId']) {
                $isAllThemesParentId = false;
                break;
            }
        }

        return $isAllThemesParentId;
    }
}
