<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProdAccountsFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Define the client accounts to be created upon deploying to production
        $prodEmails = ['jgd@thecodingmachine.com ', 'ken@thecodingmachine.com', 'nip@thecodingmachine.com',
            'sog@thecodingmachine.com', 'joa@thecodingmachine.com', 'jfb@thecodingmachine.com',
            'toa@thecodingmachine.com', 'som@thecodingmachine.com', 'alp@thecodingmachine.com'
        ];

        foreach ($prodEmails as $email) {
            $user = new User();
            $user->setEmail($email);
            // Don't forget to ask clients to use "forget password"
            $user->setPassword($this->passwordHasher->hashPassword($user, 'Ch4ngeMePle4se'));
            $user->setCompany($this->getReference(CompanyFixtures::TCM_COMPANY_REFERENCE));
            $manager->persist($user);
            $manager->flush();
        }
    }
}
