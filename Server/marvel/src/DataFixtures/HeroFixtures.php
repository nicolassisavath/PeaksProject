<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Hero;

class HeroFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $hulk = new Hero("1009351");
        $manager->persist($hulk);

        $ironMan = new Hero("1009368");
        $manager->persist($ironMan);

        $manager->flush();

        $this->addReference('hulk', $hulk);
        $this->addReference('ironMan', $ironMan);
    }
}
