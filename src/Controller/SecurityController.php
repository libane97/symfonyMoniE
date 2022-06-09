<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\CategoryRepository;
use App\Services\Cart\CartService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    protected $cartService;
   
    public function __construct(CartService $cartService)
    {
     return $this->cartService = $cartService;
    }


    /**
     * @Route("/security/register", name="security")
     */
    public function index(Request $request,  ManagerRegistry $manager, UserPasswordEncoderInterface $encoder, CategoryRepository $categoryRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
             $passwordHash = $encoder->encodePassword($user, $user->getPassword());
             $user->setPassword($passwordHash);
             $user->setCreatedAt(new \DateTime());
             $em = $manager->getManager();
             $em->persist($user);
             $em->flush(); 
             return $this->redirectToRoute('security');
        }
 
        $categories = $categoryRepository->findAll();
        return $this->render('security/index.html.twig', ['form' => $form->createView(), 
        'controller_name' => 'Creer votre compte client',
        'categories' => $categories,
        'items' => $this->cartService->getFullCart(),
        'Totale' => $this->cartService->getTotale()
    ]);
    }

    /**
    * @Route("/security/login", name="login")
    */
   public function login(AuthenticationUtils $authenticationUtils,CategoryRepository $categoryRepository, TokenStorageInterface $tokenStorage): Response
   {

        // dd($authenticationUtils);
       // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();
        
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
         $objectUserConnect = $tokenStorage->getToken();
       //  dd($objectUserConnect->getUser());
        /*   if ($lastUsername) {
            return $this->redirectToRoute('welcome');
          } */
       return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
        'categories' =>  $categories = $categoryRepository->findAll(),
        'items' => $this->cartService->getFullCart(),
        'Totale' => $this->cartService->getTotale()
       ]);
   }

    /**
     * @Route("/security/logout", name="logout")
     */
    public function logout(){}

}
