<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $repository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->repository = $categoryRepository;
    }
    /**
     * @Route("/category/all", name="all_categories")
     */
    public function all_categories(): Response
    {
        $categories = $this->repository->findAll();
        return $this->render('category/allcategory.html.twig', [
            'categories' => $categories,
        ]);
    }
    /**
     * @Route("/category/add", name="category_add")
     */
    public function category_add(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
           if ($form->isSubmitted() && $form->isValid()) {
               $em = $this->getDoctrine()->getManager();
               $em->persist($category);
               $em->flush();
               $this->addFlash('success', "La catégorie a été ajoutée");
               return $this->redirectToRoute('all_categories');
           }
        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function category_delete(int $id): Response
    {
        $category = $this->repository->find($id);
        $em =  $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', "La catégorie a été supprimée");
        return $this->redirectToRoute('all_categories');
    }
}
