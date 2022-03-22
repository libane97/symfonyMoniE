<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Services\Cart\CartService;
use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER")
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
            'controller_name' => 'gestion de categorie by OnArchive',
            'form' => $form->createView(),
            'categories' => $this->categoryRepository->selectOnArchive(),
            'items' => $this->cartService->getFullCart()
        ]);
    }

     /**
     * @IsGranted("ROLE_USER")
     * @Route("/category/inArchive", name="inarchive-category")
    */
    public function selectOnArchive()
    {
        return $this->render('category/inarchive.html.twig', [
            'controller_name' => 'gestion de categorie by InArchive',
            'categories' => $this->categoryRepository->selectInArchive(),
            'items' => $this->cartService->getFullCart()
        ]);
    }
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/category/{id}/show", name="show-category")
    */
    public function showProductsByCategory(Category $category,ManagerRegistry $manager)
    {  
        $products = $manager->getRepository(Product::class)->findBy(['category' => $category]);
        dd($products);
        
    }
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/category/{id}/onArchive", name="delete-category")
    */
    public function OnArchive($id,ManagerRegistry $manager)
    {
        $categorie = $this->categoryRepository->find($id);
        $categorie->setArchived(0);
        $em = $manager->getManager();
        $em->persist($categorie);
        $em->flush();
        return $this->redirectToRoute('category');     
    }

     /**
     * @IsGranted("ROLE_USER")
     * @Route("/category/{id}/InArchive", name="InArchive-category")
    */
    public function InArchive($id,ManagerRegistry $manager)
    {
        $categorie = $this->categoryRepository->find($id);
        $categorie->setArchived(1);
        $em = $manager->getManager();
        $em->persist($categorie);
        $em->flush();
        return $this->redirectToRoute('category');    
    }
}
