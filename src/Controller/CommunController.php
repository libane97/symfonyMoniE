<?php

namespace App\Controller;

use App\Entity\Commune;
use App\Form\CommuneType;
use App\Repository\CategoryRepository;
use App\Repository\CommuneRepository;
use App\Services\Cart\CartService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommunController extends AbstractController
{
    protected $cartService;
    protected $communeRepository;
    protected $categoryRepository;
    
    public function __construct(CommuneRepository $communeRepository,CartService $cartService, CategoryRepository $categoryRepository)
    {
        $this->communeRepository = $communeRepository;
        $this->cartService = $cartService;
        $this->categoryRepository = $categoryRepository;
    }
    
    /**
     * @Route("/commun", name="commun")
     * @Route("/commun/{id}/edite", name="commun-edit")
     */
    public function index(Commune $commune = null, Request $request, ManagerRegistry $manager): Response
    {
        if (!$commune) {
           $commune = new Commune();
        }
        $form = $this->createForm(CommuneType::class, $commune);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            /*
            * for get data view with method Form Data
              // $form->getViewData() 
            */
            if ($commune->getMontantMax() > $commune->getTarif()) {
                $commune->setCreatedAt(new \DateTime());
                $commune->setArchived(1);
                $em = $manager->getManager();
                $em->persist($commune);
                $em->flush();
                return $this->redirectToRoute('commun');

            } else {
                $this->addFlash('message', 'error', 'Oupps Le montant max doit etre plus grand que le tarif de la livraison merci de bien verifier');
                return $this->redirectToRoute('commun');
            }
            
        }
       // dd($this->communeRepository->noArchivedCommune());
        return $this->render('commun/index.html.twig', [
            'controller_name' => 'Creation une zone de livraision ',
            'form' => $form->createView(),
            'communs' => $this->communeRepository->noArchivedCommune(),
            'categories' => $this->categoryRepository->findAll(),
            'items' => $this->cartService->getFullCart()
        ]);
    }

     /**
     * @Route("/commun/{id}/delete", name="delete-commun")
    */
    public function archivedCommune(Commune $commune)
    {
        $commune->setArchived(0);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commune);
        $entityManager->flush();
        return $this->redirectToRoute('commun');     
    }
   
}
