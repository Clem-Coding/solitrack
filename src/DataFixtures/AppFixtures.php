<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use App\Factory\DonationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        CategoryFactory::createCategories();

        // Crée 5 users
        UserFactory::createMany(5);

        // Crée 10 donations en liant une catégorie et un user au hasard
        DonationFactory::createMany(10, function () {
            return [
                'weight' => rand(1, 100),
                'category' => CategoryFactory::random(),
                'user' => UserFactory::random(),
            ];
        });

        $manager->flush();
    }
}
