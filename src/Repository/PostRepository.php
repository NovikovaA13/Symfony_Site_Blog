<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function searchByQuery(string $query)
    {
        return $this->createQueryBuilder('p')
                    ->where('p.title LIKE :query')
                    ->setParameter('query', '%'.$query.'%')
                    ->getQuery()
                    ->getResult();
    }
    public function postsByCategory(string $id)
    {
        $connexion = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM post JOIN post_category ON post.id = post_category.post_id JOIN user ON post.user_id = user.id WHERE post_category.category_id = :id';
        $req = $connexion->prepare($sql);
        $req->bindParam(':id', $id);
        $req->execute();
        return $req->fetchAll();
    }
    public function categoriesByPost(string $id)
    {
        $connexion = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM category JOIN post_category ON category.id = post_category.category_id WHERE post_category.category_id = :id';
        $req = $connexion->prepare($sql);
        $req->bindParam(':id', $id);
        $req->execute();
        return $req->fetchAll();
    }
    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
