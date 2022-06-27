<?php

namespace App\Controller;

use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;


class PostController extends AbstractController
{
    /**
     * @var PostRepository $repository
     */
    private $repository;

    /**
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->repository = $postRepository;
    }

    /**
     * @Route("post/add", name="post_add")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, Slugify $slugify)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $post->setSlug($slugify->slugify($post->getTitle()));
            $post->setCreatedAt(new \DateTime());
            $post->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Votre article a été ajouté.');
            return $this->redirectToRoute('post_edit', ['slug' => $post->getSlug()]);

        }
        return $this->render('post/add.html.twig', ['form' => $form->createView()]);

    }
    /**
     * @Route("post/{slug}", methods={"POST"}, name="comment_new")
     * @IsGranted("ROLE_USER")
     */
    public function addComment(Post $post, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->addComment($comment);
            $post->addComment($comment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a été ajouté.');
            return $this->redirectToRoute('post_view', ['slug' => $post->getSlug()]);
        }
        return $this->render('post/view.html.twig', ['post' => $post, 'form' => $form->createView()]);
    }
    /**
     * @Route("/", name="all_posts")
     */
    public function posts(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->repository->findBy([], ['created_at' => 'desc']);
        $posts = $paginator->paginate($data, $request->query->getInt('page', 1), 5);
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
    /**
     * @Route("/category/{id}", name="postsByCategory")
     */
    public function postsByCategory(Request $request, PaginatorInterface $paginator, int $id): Response
    {
        $data = $this->repository->postsByCategory($id);
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $posts = $paginator->paginate($data, $request->query->getInt('page', 1), 5);
        return $this->render('post/postsbycategory.html.twig', [
            'posts' => $posts, 'category' => $category
        ]);
    }


    /**
     * @Route("post/search", name="blog_search")
     */
    public function search(Request $request, PaginatorInterface $paginator)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $query = $request->query->get('q');
        $data = $this->repository->searchByQuery($query);
        $posts = $paginator->paginate($data, $request->query->getInt('page', 1), 5);
        return $this->render('post/search_results.html.twig', ['posts' => $posts, 'form' => $form->createView()]);
    }
    /**
     * @Route("post/{slug}/edit", name="post_edit")
     * @IsGranted("POST_EDIT", subject="post")
     */
    public function edit(Post $post, Request $request, Slugify $slugify)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $all_categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugify->slugify($post->getTitle()));
            $post->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Votre article a été modifié.');
            return $this->redirectToRoute('post_edit', ['slug' => $post->getSlug()]);
        }
        return $this->render('post/edit.html.twig',  ['post' => $post, 'form' => $form->createView(), 'all_categories' => $all_categories]);

    }

    /**
     * @Route("post/{slug}/delete", name="post_delete")
     * @IsGranted("POST_EDIT", subject="post")
     */
    public function delete(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Votre article a été supprimé.');
        return $this->redirectToRoute('all_posts');
    }


    /**
     * @Route("delete/{id}", name="delete_comment")
     * @IsGranted("COMMENT_EDIT", subject="comment")
     */
    public function deleteComment(Comment $comment)
    {
        $slugPost = $comment->getPost()->getSlug();
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'Votre commentaire a été supprimé.');
        return $this->redirectToRoute("post_view", ['slug' => $slugPost]);
    }
    /**
     * @Route("delete_category/{slug}/{category_id}", name="delete_category")
     * @IsGranted("POST_EDIT", subject="post")
     */
    public function deleteCategory(Post $post, int $category_id)
    {
        $form = $this->createForm(PostType::class, $post);
        $category = $this->getDoctrine()->getRepository(Category::class)->find($category_id);
        $all_categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $post->removeCategory($category);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        $this->addFlash('success', 'La catégorie a été supprimée.');
        return $this->render('post/edit.html.twig',  ['post' => $post, 'form' => $form->createView(), 'all_categories' => $all_categories]);
    }
    /**
     * @Route("add_category/{slug}/{category_id}", name="add_category")
     * @IsGranted("POST_EDIT", subject="post")
     */
    public function addCategory(Post $post, int $category_id)
    {
        $form = $this->createForm(PostType::class, $post);
        $category = $this->getDoctrine()->getRepository(Category::class)->find($category_id);
        $all_categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $post->addCategory($category);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        $this->addFlash('success', 'La catégorie a été ajoutée.');
        return $this->render('post/edit.html.twig',  ['post' => $post, 'form' => $form->createView(), 'all_categories' => $all_categories]);
    }

    /**
     * @Route("post/{slug}", methods={"GET"}, name="post_view")
     */
    public function view(Post $post)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        return $this->render('post/view.html.twig',  ['post' => $post, 'form' => $form->createView()]);

    }


}
