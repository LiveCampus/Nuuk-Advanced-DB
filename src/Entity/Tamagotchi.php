<?php

namespace App\Entity;

use App\Repository\TamagotchiRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TamagotchiRepository::class)]
class Tamagotchi
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $hunger = 70;

    #[ORM\Column]
    private ?int $thirst = 70;

    #[ORM\Column]
    private ?int $sleep = 70;

    #[ORM\Column]
    private ?int $boredom = 70;

    #[ORM\Column]
    private ?bool $alive = true;

    #[ORM\Column]
    private ?int $level = 1;

    #[ORM\Column]
    private ?int $actions_count = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $diedOn = null;

    #[ORM\ManyToOne(inversedBy: 'tamagotchis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column]
    private ?bool $first = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(timezone: new \DateTimeZone("Europe/Paris"));
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

    public function getHunger(): ?int
    {
        return $this->hunger;
    }

    public function setHunger(int $hunger): self
    {
        $this->hunger = $hunger;

        return $this;
    }

    public function getThirst(): ?int
    {
        return $this->thirst;
    }

    public function setThirst(int $thirst): self
    {
        $this->thirst = $thirst;

        return $this;
    }

    public function getSleep(): ?int
    {
        return $this->sleep;
    }

    public function setSleep(int $sleep): self
    {
        $this->sleep = $sleep;

        return $this;
    }

    public function getBoredom(): ?int
    {
        return $this->boredom;
    }

    public function setBoredom(int $boredom): self
    {
        $this->boredom = $boredom;

        return $this;
    }

    public function isAlive(): ?bool
    {
        return $this->alive;
    }

    public function setAlive(bool $alive): self
    {
        $this->alive = $alive;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getActionsCount(): ?int
    {
        return $this->actions_count;
    }

    public function setActionsCount(int $actions_count): self
    {
        $this->actions_count = $actions_count;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDiedOn(): ?\DateTimeImmutable
    {
        return $this->diedOn;
    }

    public function setDiedOn(?\DateTimeImmutable $diedOn): self
    {
        $this->diedOn = $diedOn;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Si valide, fait manger le tamagotchi et incrémente le nombre d'actions
     *
     * @return bool
     */
    public function eat(): bool
    {
        if ($this->hunger <= 80) {
            /*
             * Faire manger le tamagotchi fait :
             * faim + 30 || soifs - 10 || sommeil - 5 || ennui - 5
             */

            $this->hunger = min($this->hunger + $this->level + 30, 100);
            $this->thirst = max($this->thirst - (10 + $this->level), 0);
            $this->sleep = max($this->sleep - (5 + $this->level), 0);
            $this->boredom = max($this->boredom - (5 + $this->level), 0);

            $this->incrementAction();
            return true;
        }

        return false;
    }

    /**
     * Si valide, fait boire le tamagotchi et incrémente le nombre d'actions
     *
     * @return bool
     */
    public function drink(): bool
    {
        if ($this->thirst <= 80) {
            /*
             * Faire boire le tamagotchi fait :
             * soifs + 30 || faim - 10 || sommeil - 5 || ennui - 5
             */

            $this->thirst = min($this->thirst + $this->level + 30, 100);
            $this->hunger = max($this->hunger - (10 + $this->level), 0);
            $this->sleep = max($this->sleep - (5 + $this->level), 0);
            $this->boredom = max($this->boredom - (5 + $this->level), 0);

            $this->incrementAction();
            return true;
        }

        return false;
    }

    /**
     * Si valide, fait dormir le tamagotchi et incrémente le nombre d'actions
     *
     * @return bool
     */
    public function goSleep(): bool
    {
        if ($this->sleep <= 80) {
            /*
             * Faire dormir le tamagotchi fait :
             * sommeil + 30 || soifs - 15 || ennui - 15 || faim - 10
             */

            $this->sleep = min($this->sleep + $this->level + 30, 100);
            $this->thirst = max($this->thirst - (15 + $this->level), 0);
            $this->boredom = max($this->boredom - (15 + $this->level), 0);
            $this->hunger = max($this->hunger - (10 + $this->level), 0);

            $this->incrementAction();
            return true;
        }

        return false;
    }

    /**
     * Si valide, fait jouer le tamagotchi et incrémente le nombre d'actions
     *
     * @return bool
     */
    public function play(): bool
    {
        if ($this->boredom <= 80) {
            /*
             * Faire jouer le tamagotchi fait :
             * ennui + 15 || soifs - 5 || sommeil - 5 || faim - 5
             */

            $this->boredom = min($this->boredom + $this->level + 15, 100);
            $this->thirst = max($this->thirst - (5 + $this->level), 0);
            $this->sleep = max($this->sleep - (5 + $this->level), 0);
            $this->hunger = max($this->hunger - (5 + $this->level), 0);

            $this->incrementAction();
            return true;
        }

        return false;
    }

    /**
     * Incrémente le nombre d'actions et incrément le level du tamagotchi si le nombre d'actions est au-dessus de 10
     *
     * @return void
     */
    private function incrementAction(): void
    {
        $this->actions_count++;

        if ($this->actions_count >= 10) {
            $this->level ++;
            $this->actions_count = 0;
        }
    }

    public function isFirst(): ?bool
    {
        return $this->first;
    }

    public function setFirst(bool $first): self
    {
        $this->first = $first;

        return $this;
    }
}
