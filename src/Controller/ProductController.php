<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Services\Cart\CartService;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Services\File\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProductController extends AbstractController
{
    protected $categoryRepository;
    protected $cartService;
    public function __construct(CategoryRepository $categoryRepository, CartService $cartService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/product", name="product")
     */
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/product/new", name="product-new")
     * @Route("/product/{id}/edit", name="update")
     */
    public function createAndStore(Product $product = null, Request $request, FileUploader $fileUploader, ManagerRegistry $manager, ProductRepository $productRepository): Response
    {
        $search = $request->request->get('search');
        if (!$product) {
            $product = new Product();
        }

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
       // dd($form);
        if ($form->isSubmitted() && $form->isValid()) { 
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setPhoto($brochureFileName);
            }
           // $product->setArchived(1);
            $product->setCreatedAt(new \DateTime());
            $em = $manager->getManager();
            $em->persist($product);
            $em->flush();
        }
        return $this->render('product/index.html.twig', [
            'categories' =>  $categories = $this->categoryRepository->findAll(),
            'items' => $this->cartService->getFullCart(),
            'form' => $form->createView(),
            'products' => $this->search($search)
        ]);
    }


    private function search($search)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);

        if ($search != null) {
            $products = $repo->findBy(['libelle' => $search]);    
        } else {
            $products = $repo->findAll();
        }

        return $products;
    }
}
