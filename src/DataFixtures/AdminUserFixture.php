<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture implements DependentFixtureInterface
{
    
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            'admin@security-playground.localhost',
            'axel@monsite.com',
            'fadi@monsite.com',
            'pierre@monsite.com',
            'marie@monsite.com',
            'sabrina@monsite.com',
        ];

        foreach ($users as $userEmail) {
            $admin = new User();
            $admin->setEmail($userEmail);
            $admin->setPassword($this->passwordHasher->hashPassword(
                $admin,
                '9d8d!02ND892'
            ));
            $admin->setCompany($this->getReference(CompanyFixtures::TCM_COMPANY_REFERENCE));
            $manager->persist($admin);
            $manager->flush();
        }


    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class
        ];
    }


}
