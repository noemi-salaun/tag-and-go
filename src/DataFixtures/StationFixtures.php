<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class StationFixtures
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class StationFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        // Real data from https://www.coordonnees-gps.fr/
        $allStationsData = [
            'Nantes' => [
                [
                    'name' => 'Union', 'address' => "Rue de l'Union",
                    'latitude' => 47.215851154041154, 'longitude' => -1.5506450297466472,
                    'bikesCapacity' => 5, 'bikesAvailable' => 3,
                ],
                [
                    'name' => 'Hotel-Dieu', 'address' => 'Place Alexis-Ricordeau',
                    'latitude' => 47.21195818961807, 'longitude' => -1.5543762286920355,
                    'bikesCapacity' => 5, 'bikesAvailable' => 5,
                ],
                [
                    'name' => 'Saint-Nicolas', 'address' => 'Rue Affre',
                    'latitude' => 47.215428977129726, 'longitude' => -1.5577411651611328,
                    'bikesCapacity' => 3, 'bikesAvailable' => 0,
                ],
            ],
            'Paris' => [
                [
                    'name' => 'Arc de Triomphe', 'address' => "Place Charles de Gaulle",
                    'latitude' => 48.87397380289261, 'longitude' => 2.294769287109375,
                    'bikesCapacity' => 8, 'bikesAvailable' => 6,
                ],
                [
                    'name' => 'Tour Eiffel', 'address' => 'Avenue Anatole',
                    'latitude' => 48.8583905296204, 'longitude' => 2.2944259643554688,
                    'bikesCapacity' => 8, 'bikesAvailable' => 3,
                ],
                [
                    'name' => 'Boulogne', 'address' => 'Route de la Grande Cascade',
                    'latitude' => 48.862456199187356, 'longitude' => 2.2491073608398438,
                    'bikesCapacity' => 4, 'bikesAvailable' => 4,
                ],
            ],
        ];

        // We set a fake creation date so our tests can stay relevant over time.
        $fakeNow = new \DateTime('2018-01-01 00:00:00', new \DateTimeZone('Europe/Paris'));

        foreach ($allStationsData as $cityName => $cityStationsData) {
            /** @var City $city */
            $city = $this->getReference('city-' . $cityName);

            foreach ($cityStationsData as $cityStationData) {
                $station = new Station(
                    $city,
                    $fakeNow,
                    $cityStationData['name'],
                    $cityStationData['address'],
                    $cityStationData['latitude'],
                    $cityStationData['longitude'],
                    $cityStationData['bikesCapacity'],
                    $cityStationData['bikesAvailable'],
                    true
                );
                $station->setActivated(true);
                $station->setBikesAvailable($cityStationData['bikesAvailable']);
                $manager->persist($station);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // Stations need to be created just after cities, with order 1.
        return 2;
    }
}
