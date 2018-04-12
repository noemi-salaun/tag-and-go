<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StationRepository")
 */
class Station
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $city;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"read"})
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"read"})
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private $address;

    /**
     * @ORM\Column(type="float")
     *
     * @Groups({"read"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     *
     * @Groups({"read"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private $bikesCapacity;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
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
        int $bikesCapacity,
        int $bikesAvailable,
        bool $activated
    ) {
        $this->city           = $city;
        $this->creationDate   = $creationDate;
        $this->lastUpdate     = $creationDate;
        $this->name           = $name;
        $this->address        = $address;
        $this->latitude       = $latitude;
        $this->longitude      = $longitude;
        $this->bikesCapacity  = $bikesCapacity;
        $this->bikesAvailable = $bikesAvailable;
        $this->activated      = $activated;
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

    public function decrementBikesAvailable(): int
    {
        $this->bikesAvailable--;

        return $this->bikesAvailable;
    }

    public function incrementBikesAvailable(): int
    {
        $this->bikesAvailable++;

        return $this->bikesAvailable;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
    }
}
