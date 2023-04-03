<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CompanyHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanyHistory>
 *
 * @method CompanyHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyHistory[]    findAll()
 * @method CompanyHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyHistory::class);
    }

    public function save(CompanyHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CompanyHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllBySymbolDates(string $symbol, string $startDate, string $endDate): array
    {
        return $this->createQueryBuilder('c')
                    ->andWhere('c.symbol = :val')
                    ->andWhere('c.date > :startDate')
                    ->andWhere('c.date < :endDate')
                    ->setParameter('val', $symbol)
                    ->setParameter('startDate', $startDate)
                    ->setParameter('endDate', $endDate)
                    ->getQuery()
                    ->getResult();

    }

    /**
     * @throws NonUniqueResultException
     */
    public function findLastOne(): ?CompanyHistory
    {
        return $this->createQueryBuilder('c')
                    ->orderBy('c.id', 'DESC')
                    ->getQuery()
                    ->setMaxResults(1)
                    ->getOneOrNullResult()
            ;
    }
}
