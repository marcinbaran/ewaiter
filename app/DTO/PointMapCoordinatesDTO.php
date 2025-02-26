<?php

namespace App\DTO;

final readonly class PointMapCoordinatesDTO
{
    public function __construct(
        private string $lat,
        private string $lng,
        private string $address = ''
    ) {
    }

    public function __toString()
    {
        return $this->lat.','.$this->lng;
    }

    public function toArray()
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    public function getLat(): string
    {
        return $this->lat;
    }

    public function getLng(): string
    {
        return $this->lng;
    }

    public function getAddress()
    {
        return $this->address;
    }
}
