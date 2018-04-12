<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class CityRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * Finds the cities for a given page and limit.
     *
     * @return City[]
     */
    public function findPage(int $page, int $limit): array
    {
        return $this->createQueryBuilder('city')
            ->where('city.activated = true')
            ->orderBy('city.id', 'ASC')
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
