<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Cart\CartService;
class CartController extends AbstractController
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
      return  $this->cartService = $cartService;
    }
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CategoryRepository $categoryRepository,ProductRepository  $productRepository): Response
    {
      //  $category = $request->request->get('category');
        $categories = $categoryRepository->findAll();
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'categories' => $categories,
            'items' => $this->cartService->getFullCart(),
            'Totale' => $this->cartService->getTotale(),
            'products' => $productRepository->findAll(),
        ]);
    }


    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add($id)
    { 
        $this->cartService->add($id);
        return $this->redirectToRoute('welcome');
    }

     /**
     * @Route("/cart/addnew/{id}", name="cart_add_new")
     */
    public function cart_add_new($id)
    {
        $this->cartService->add($id);
        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove($id)
    {
        $this->cartService->removeOneItem($id);
        return $this->redirectToRoute('cart');
    }

     /**
     * @Route("/cart/removeitem/{id}", name="cart_remove_item")
     */
    public function remove_item($id, CartService $cartService)
    {   
        $this->cartService->remove($id);
        return $this->redirectToRoute('cart');
    }


}
