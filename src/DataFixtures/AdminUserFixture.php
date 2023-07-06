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
        $admin = new User();
        $admin->setEmail('admin@security-playground.localhost');
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'secret'
        ));
        $admin->setCompany($this->getReference(CompanyFixtures::TCM_COMPANY_REFERENCE));
        $manager->persist($admin);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class
        ];
    }


}
