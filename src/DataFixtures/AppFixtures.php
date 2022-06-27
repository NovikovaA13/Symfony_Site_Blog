<?php

namespace App\DataFixtures;

use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\CommentFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public const POST_REFERENCED = 'post';
    private $faker;
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->faker = Factory::create();
        $this->slugify= $slugify;
    }
    public function load(ObjectManager $manager): void
    {

        $this->loadPosts($manager);
    }
    public function loadCategories(ObjectManager $manager)
    {
        $categories = [ 1 => 'laudantium', 'aperiam', 'voluptatem', 'consectetur', 'tempora', 'consequatur', 'pariatur', 'reprehenderit', 'molestiae', 'denouncing'];
        for ($i = 1; $i <= 10; $i++) {
            $category = new Category();
            $category->setName($categories[$i]);
            $manager->persist($category);
            $catNum = 'category_'.$i;
            $this->addReference($catNum, $category);
        }
        $manager->flush();
    }
    public function loadPosts(ObjectManager $manager)
    {
        $this->loadCategories($manager);

        for ($i = 1; $i <= 500; $i++) {
            $post = new Post();
            $post->setTitle($this->faker->text(100));
            $rand = rand(1, 20);
            $user = $this->getReference('user_'.$rand);
            $post->setUser($user);

            for ($j = 1; $j <= 4; $j++) {
                $randCat = rand(1, 10);
                $category = $this->getReference('category_'.$randCat);
                $post->addCategory($category);
            }
            $post->setBody($this->faker->text(1000));
            $post->setSlug($this->slugify->slugify($post->getTitle()));
            $post->setCreatedAt($this->faker->dateTimeBetween('-100 days', 'now'));
            $manager->persist($post);
            $postNum = 'post_'.$i;
            $this->addReference($postNum, $post);

        }

        $manager->flush();
    }
    public function getDependencies()
	{
        return [
            UserFixtures::class,
        ];
    }
}
