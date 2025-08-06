<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'admin@solitrack.fr',
                'roles' => ['ROLE_ADMIN'],
                'firstName' => 'Admin',
                'lastName' => 'Admin',
            ],
            [
                'email' => 'benevole-plus@solitrack.fr',
                'roles' => ['ROLE_VOLUNTEER_PLUS'],
                'firstName' => 'Benevole',
                'lastName' => 'Plus',
            ],
            [
                'email' => 'user@solitrack.fr',
                'roles' => [],
                'firstName' => 'User',
                'lastName' => 'Example',
            ],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            // hash du mot de passe "password"
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
