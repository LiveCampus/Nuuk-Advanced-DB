<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Owner
{
    private ?int $id = null;
    private ?string $name = null;
    private Collection $tamagotchis;

    public function __construct()
    {
        $this->tamagotchis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Tamagotchi>
     */
    public function getTamagotchis(): Collection
    {
        return $this->tamagotchis;
    }

    public function addTamagotchi(Tamagotchi $tamagotchi): self
    {
        if (!$this->tamagotchis->contains($tamagotchi)) {
            $this->tamagotchis->add($tamagotchi);
            $tamagotchi->setOwner($this);
        }

        return $this;
    }

    public function removeTamagotchi(Tamagotchi $tamagotchi): self
    {
        if ($this->tamagotchis->removeElement($tamagotchi)) {
            // set the owning side to null (unless already changed)
            if ($tamagotchi->getOwner() === $this) {
                $tamagotchi->setOwner(null);
            }
        }

        return $this;
    }
}
