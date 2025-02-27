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

    public function checkAllParentIdNotNull(): bool
    {
        $nullCount = $this->createQueryBuilder('t')
        ->select('COUNT(t.id)')
        ->where('t.id >= :startId')
        ->andWhere('t.parentId IS NOT NULL')
        ->setParameter('startId', 2)
        ->getQuery()
        ->getSingleScalarResult();

        return 0 === $nullCount ? false : true;
    }

    public function getParentIdByparentExternalId(string $parentExternalId): ?int
    {
        $result = $this
        ->createQueryBuilder('t')
        ->select('t.id')
        ->where('t.externalId = :externalId')
        ->setParameter('externalId', $parentExternalId)
        ->getQuery()
        ->getOneOrNullResult();

        return $result['id'] ?? null;
    }
}
