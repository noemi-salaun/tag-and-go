<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationRepository extends ServiceEntityRepository
{

    /** The scaling to convert kilometers to latitude/longitude. */
    private const SCALE_FROM_KM = 6371;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Station::class);
    }

    /**
     * Finds the stations of a city, for a given page and limit.
     *
     * @return Station[]
     */
    public function findPage(City $city, int $page, int $limit): array
    {
        return $this->createQueryBuilder('station')
            ->where('station.activated = true')
            ->andWhere('station.city = :city')->setParameter('city', $city)
            ->orderBy('station.id', 'ASC')
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds the stations near some coordinates.
     *
     * @return Station[]
     */
    public function findNear(float $latitude, float $longitude, int $radius): array
    {
        // Algorithm taken from https://stackoverflow.com/questions/7783684/select-coordinates-which-fall-within-a-radius-of-a-central-point

//        return $this->createQueryBuilder('station')
//            ->where('station.activated = true')
//            ->andWhere('acos(sin(station.latitude * 0.0175) * sin(:latitude * 0.0175)
//                    + cos(station.latitude * 0.0175) * cos(:latitude * 0.0175) *
//                    cos((:longitude * 0.0175) - (station.longitude * 0.0175))
//                ) * 6371 <= :radius')
//            ->orderBy('station.id', 'ASC')
//            ->setParameters([
//                'latitude' => $latitude,
//                'longitude' => $longitude,
//                'radius' => $radius,
//            ])
//            ->getQuery()
//            ->getResult();
    }
}
