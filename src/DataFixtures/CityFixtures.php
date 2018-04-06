<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CityFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Real data from https://www.coordonnees-gps.fr/
        $citiesData = [
            ['name' => 'Nantes', 'latitude' => 47.218371, 'longitude' => -1.553621000000021],
            ['name' => 'Paris', 'latitude' => 48.85661400000001, 'longitude' => 2.3522219000000177],
        ];

        foreach ($citiesData as $cityData) {
            $city = new City($cityData['name'], $cityData['latitude'], $cityData['longitude']);
            $manager->persist($city);

            // Keep a reference to the city, to be used in the stations fixtures.
            $this->setReference('city-'.$cityData['name'], $city);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // Stations have a relationship with cities, so cities need to be created first.
        return 1;
    }
}
