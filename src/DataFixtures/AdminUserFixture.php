<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\CompanyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture implements DependentFixtureInterface
{
    
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly CompanyRepository $companyRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $companies = $this->companyRepository->findAll();

        for ($i = 0; $i < 20; $i++) {
            $user = new User();

            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'secret'));
            $user->setCompany($faker->randomElement($companies));
            $user->addRole('ROLE_USER');

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class
        ];
    }
}
