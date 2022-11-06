<?php

namespace App\DataFixtures;

use App\Entity\Tamagotchi;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class TamagotchiFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE = "user-";
    private Generator $generator;

    public function __construct() {
        $this->generator = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $tamagotchi = new Tamagotchi();
                $tamagotchi->setName($this->generator->firstName);
                $tamagotchi->setOwner($this->getReference(self::USER_REFERENCE . $i));

                $manager->persist($tamagotchi);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}