<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationRepository extends ServiceEntityRepository
{

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
        // Prepare the result set to contains Station entities.
        $rsmBuilder = new ResultSetMappingBuilder($this->_em);
        $rsmBuilder->addRootEntityFromClassMetadata(Station::class, 'station');
        $selectClause = $rsmBuilder->generateSelectClause();

        // Get the Station table name to prevent breaking changes in case of table renaming.
        $tableName = $this->_class->getTableName();

        // Algorithm taken from https://stackoverflow.com/questions/7783684/select-coordinates-which-fall-within-a-radius-of-a-central-point
        $sql = "
SELECT $selectClause FROM $tableName as station
WHERE station.activated = 1
AND (acos(sin(station.latitude * 0.0175) * sin(:latitude * 0.0175)
    + cos(station.latitude * 0.0175) * cos(:latitude * 0.0175) *
    cos((:longitude * 0.0175) - (station.longitude * 0.0175))
  ) * 6371 <= :radius)
ORDER BY station.id
";

        $query = $this->_em->createNativeQuery($sql, $rsmBuilder);
        $query->setParameters([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius' => $radius,
        ]);

        return $query->getResult();
    }
}
