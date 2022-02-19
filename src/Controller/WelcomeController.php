<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;

class WelcomeController extends AbstractController
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("/", name="welcome")
     */
    public function index(CategoryRepository $categoryRepository, Request $request, ProductRepository $productRepository, SessionInterface $session,  PaginatorInterface $paginator): Response
    {
        $category = $request->request->get('category');
        $search = $request->request->get('search');
        
        $categories = $categoryRepository->findAll();
        $repo = $this->getDoctrine()->getRepository(Product::class);
       
        // }elseif ($search != null){
        //     $products = $repo->findBy(['libelle' => $search]);
           
        // }elseif ($category != null && $search != null) {
        //     $products = $repo->findBy(['category' => $category ,'libelle' => $search]);
        // }
        if ($search != null) {
            // $products = $repo->findAll();
             $products = $productRepository->findByLibelle($search);
            //  dd($products);
            //  die();
        }elseif ($category != null) {
            $products = $repo->findBy(['category' => $category]);
        }
        else {
            $products = $repo->findAll();
        }
        
        $products = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        // gestion panier 
        $panier = $session->get('panier', []); 
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $Totale = 0;
        foreach ($panierWithData as $item) {
            $itemTotale = $item['product']->getPrice() * $item['quantity'];
            $Totale += $itemTotale;
        }

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'categories' => $categories,
            'products' => $products,
            'items' => $panierWithData,
            'search' => $search,
            'user' =>  $user = $this->security->getUser()
          
        ]);
    }


    /**
     * @Route("/product/{id}", name="show")
     */
    public function show(Product $product, CategoryRepository $categoryRepository,  Request $request, ProductRepository $productRepository, SessionInterface $session): Response
    {
        $categories = $categoryRepository->findAll();
        $category = $request->request->get('category');
        $search = $request->request->get('search');
        
        $categories = $categoryRepository->findAll();
        $repo = $this->getDoctrine()->getRepository(Product::class);
        // dd($product->getCategory());
        // die();

        // Gestion de panier
         // gestion panier 
         $panier = $session->get('panier', []); 
         $panierWithData = [];
         foreach ($panier as $id => $quantity) {
             $panierWithData[] = [
                 'product' => $productRepository->find($id),
                 'quantity' => $quantity
             ];
         }
         $Totale = 0;
         foreach ($panierWithData as $item) {
             $itemTotale = $item['product']->getPrice() * $item['quantity'];
             $Totale += $itemTotale;
         }
        if ($product->getCategory() != null) {
           // $products = $repo->findAll();
            $products = $repo->findBy(['category' => $product->getCategory()]);
           // return $this->redirectToRoute('welcome', ['products' => $products]);
        }
        else {
            $products = $repo->findAll();
        }
        return $this->render('welcome/show.html.twig', ['product' => $product, 'categories' => $categories, 'products' => $products, 'items'=> $panierWithData]);
    }

}