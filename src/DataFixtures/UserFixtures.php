<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = "user-";
    private Generator $generator;

    public function __construct() {
        $this->generator = Factory::create("fr_FR");
    }
    
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setName($this->generator->firstName);
            $this->addReference(self::USER_REFERENCE . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}