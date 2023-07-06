<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{

    const TCM_COMPANY_REFERENCE = 'tcm_company';

    public function load(ObjectManager $manager): void
    {
        $tcmCompany = new Company();
        $tcmCompany->setName('The Coding Machine');
        $manager->persist($tcmCompany);
        $this->addReference(self::TCM_COMPANY_REFERENCE, $tcmCompany);

        foreach (['Google', 'Amazon', 'Tesla'] as $companyName){
            $company = new Company();
            $company->setName($companyName);
            $manager->persist($company);
        }

        $manager->flush();
    }

}
