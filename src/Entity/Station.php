<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StationRepository")
 */
class Station
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     */
    private $city;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $bikesCapacity;

    /**
     * @ORM\Column(type="integer")
     */
    private $bikesAvailable;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activated = false;

    public function __construct(
        City $city,
        \DateTime $creationDate,
        string $name,
        string $address,
        float $latitude,
        float $longitude,
        int $bikesCapacity
    )
    {
        $this->city = $city;
        $this->creationDate = $creationDate;
        $this->lastUpdate = $creationDate;
        $this->name = $name;
        $this->address = $address;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->bikesCapacity = $bikesCapacity;
        $this->bikesAvailable = $bikesCapacity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTime $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getBikesCapacity(): int
    {
        return $this->bikesCapacity;
    }

    public function setBikesCapacity(int $bikesCapacity): void
    {
        $this->bikesCapacity = $bikesCapacity;
    }

    public function getBikesAvailable(): int
    {
        return $this->bikesAvailable;
    }

    public function setBikesAvailable(int $bikesAvailable): void
    {
        $this->bikesAvailable = $bikesAvailable;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }
}
