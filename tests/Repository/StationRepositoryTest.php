<?php


namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class StationRepositoryTest
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class StationRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindNear()
    {
        $stationRepo = $this->entityManager->getRepository('App:Station');

        // Test with the address 61 Rue Général Buat, Nantes (47.229942, -1.539754)
        // -> Union station ~1.8km
        // -> Hotel-Dieu ~2.5km
        // -> Saint-Nicolas ~2.3km
        $latitude = 47.229942;
        $longitude = -1.539754;

        $stations = $stationRepo->findNear($latitude, $longitude, 1);

        $this->assertEmpty($stations);

        $stations = $stationRepo->findNear($latitude, $longitude, 2);

        $this->assertCount(1, $stations);
        $station = $stations[0];
        $this->assertEquals('Nantes', $station->getCity()->getName());
        $this->assertEquals('Union', $station->getName());
    }

    public function testFindFar()
    {
        $stationRepo = $this->entityManager->getRepository('App:Station');

        // Center of the France (47.029191, 2.166587)
        $latitude = 47.029191;
        $longitude = 2.166587;

        $stations = $stationRepo->findNear($latitude, $longitude, 1000);

        // It should contain all the stations in the default fixtures.
        $this->assertCount(6, $stations);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}