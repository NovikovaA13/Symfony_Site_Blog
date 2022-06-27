<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\User;
use App\Entity\Comment;
use App\DataFixtures\AppFixtures;


class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public const COMMENT_REFERENCED = 'comment';
    private $faker;
    public function __construct()
    {
        $this->faker = Factory::create();
    }
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 2000; $i++) {
            $comment = new Comment();
            $comment->setComment($this->faker->text(300));
            $r1 = 'user_' . rand(1, 20);
            $r2 = 'post_' . rand(1, 500);
            $user = $this->getReference($r1);
            $post = $this->getReference($r2);
            $comment->setUser($user);
            $comment->setPost($post);
            $manager->persist($comment);
        }
        $this->addReference(self::COMMENT_REFERENCED, $comment);
        $manager->flush();
    }
    public function getDependencies()
	{
        return [
            UserFixtures::class,
            AppFixtures::class,
        ];
    }
}