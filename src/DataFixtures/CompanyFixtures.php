<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (['The Coding Machine', 'Google', 'Amazon', 'Tesla'] as $companyName){
            $company = new Company();

            $company->setName($companyName);

            $manager->persist($company);
        }

        $manager->flush();
    }

}
