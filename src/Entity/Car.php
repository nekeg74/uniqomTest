<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarRepository")
 */
class Car
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $model;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLeft;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getIsLeft(): ?bool
    {
        return $this->isLeft;
    }

    public function setIsLeft(bool $isLeft): self
    {
        $this->isLeft = $isLeft;

        return $this;
    }
}
