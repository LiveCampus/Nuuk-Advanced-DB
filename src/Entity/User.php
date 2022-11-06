<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Tamagotchi::class, orphanRemoval: true)]
    private Collection $tamagotchis;

    public function __construct()
    {
        $this->tamagotchis = new ArrayCollection();
    }

    public function getId(): ?Uuid
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
