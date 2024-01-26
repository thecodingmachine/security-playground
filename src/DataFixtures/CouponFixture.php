<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Coupon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupon = new Coupon();

        $coupon->setCode('VIP10');
        $coupon->setPercent(10);
        $coupon->setExpirationDate((new \DateTime())->modify('+1 month'));

        $manager->persist($coupon);

        $manager->flush();
    }
}
