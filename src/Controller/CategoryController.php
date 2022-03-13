<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Services\Cart\CartService;
use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{

    protected $categoryRepository;
    protected $cartService;
    
    public function __construct(CategoryRepository $categoryRepository, CartService $cartService)
    {
        
        $this->categoryRepository = $categoryRepository;
        $this->cartService = $cartService;
    }
    /**
     * @Route("/category", name="category")
     * @Route("/category/{id}/edit", name="update-category")
     */
    public function index(Category $category = null,  Request $request, ManagerRegistry $manager): Response
    {
        if (!$category) {
            $category = new Category();
        }
        
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $category->setArchived(1);
            $em = $manager->getManager();
            $em->persist($category);
            $em->flush();
        return $this->redirectToRoute('category');
        }
        return $this->render('category/index.html.twig', [
            'controller_name' => 'Categorie',
            'form' => $form->createView(),
            'categories' => $this->categoryRepository->findAll(),
            'items' => $this->cartService->getFullCart()
        ]);
    }


    /**
     * @Route("/category/{id}/delete", name="delete-category")
    */
    public function delete(Category $category)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('category');     
    }
}
