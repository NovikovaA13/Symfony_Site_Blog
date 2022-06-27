<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\User;


class UserFixtures extends Fixture
{
    public const USER_REFERENCED = 'user';
    private $faker;
    public function __construct()
    {
        $this->faker = Factory::create();
    }
    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setName($this->faker->name);
            $user->setPassword('$2y$13$SsWHK4i7t4RgdEYUBnRjLemV5Tk1C1Kr.JWKfAS5UhgGkXrJulxoa');
            $user->setEnable(true);
            $manager->persist($user);
            $userNum = 'user_'.$i;
            $this->addReference($userNum, $user);

        }
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setName('Admin');
        $user->setPassword('$2y$13$SsWHK4i7t4RgdEYUBnRjLemV5Tk1C1Kr.JWKfAS5UhgGkXrJulxoa');
        $user->setEnable(true);
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();


    }

}