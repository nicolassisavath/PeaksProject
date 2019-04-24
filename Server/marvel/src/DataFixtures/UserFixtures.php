<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\User;
// use App\Entity\Hero;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
    	$hulk = $this->getReference('hulk');
    	$ironMan = $this->getReference('ironMan');

        $user1 = new User();
        $user1->setLogin("peaks")
              ->setPassword(password_hash("peaks", PASSWORD_DEFAULT))
              ->setFavoritesNumber(0)
              ->addHero($hulk)
              ->addHero($ironMan);

        $manager->persist($user1);

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return array(
            HeroFixtures::class
        );
    }
}
