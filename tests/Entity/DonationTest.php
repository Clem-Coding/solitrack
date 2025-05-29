<?php

namespace App\Tests\Entity;

use App\Entity\Donation;
use App\Entity\User;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class DonationTest extends TestCase
{
    public function testDonationProperties()
    {
        $donation = new Donation();


        $donation->setWeight(12.3);
        $this->assertEquals(12.3, $donation->getWeight());


        $date = new \DateTimeImmutable('2025-05-24');
        $donation->setCreatedAt($date);
        $this->assertSame($date, $donation->getCreatedAt());


        $user = new User();
        $donation->setUser($user);
        $this->assertSame($user, $donation->getUser());


        $category = new Category();
        $donation->setCategory($category);
        $this->assertSame($category, $donation->getCategory());
    }
}
