<?php

namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\CommuneRepository;
use App\Repository\OrderedRepository;
use App\Services\Mailer\MailerService;
use App\Services\Cart\CartService;
use App\Form\CustomerFormType;
use App\Entity\Customer;
use App\Entity\Ordered;
use App\Entity\OrderedDetail;
use App\Entity\Commune;
use App\Entity\Livraison;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class OrderedController extends AbstractController
{
    protected $categoryRepository;
    protected $cartService;
    protected $communeRepository;
    protected $orderedRepository;
    protected $mailer;
    protected $mailerService;
    
    public function __construct(CategoryRepository $categoryRepository, CartService $cartService, CommuneRepository $communeRepository,
     OrderedRepository $orderedRepository,
     MailerInterface $mailer,
     MailerService $mailerService
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->cartService = $cartService;
        $this->communeRepository = $communeRepository;
        $this->orderedRepository = $orderedRepository;
        $this->mailer= $mailer;
        $this->mailerService = $mailerService;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ordereds", name="ordereds")
     */
    public function orderedAll(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Ordered::class);
        $ordereds = $repo->findAll();
        return $this->render('ordered/ordered.html.twig', [
            'categories' =>  $categories = $this->categoryRepository->findAll(),
            'items' => $this->cartService->getFullCart(),
            'ordereds' => $ordereds
        ]);
    }


    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ordereds/{id}/show", name="orderedsRun", methods={"POST","GET"})
     */
    public function show(int $id, Ordered $ordered)
    {
       
        $repo = $this->getDoctrine()->getRepository(OrderedDetail::class);
        $orderedDetail = $repo->findBy(['ordered' => $id]);
        return $this->render('ordered/show.html.twig', ['orderedDetail' => $orderedDetail, 
        'categories' =>  $categories = $this->categoryRepository->findAll(),
        'items' => $this->cartService->getFullCart(),
        'ordered' => $ordered
        ]);
    }

   
    /**
     * @Route("/ordered", name="ordered")
     * @Route("/ordered/customer", name="ordered-get")
     */
    public function createAndStore(Request $request, Customer $customer = null, ManagerRegistry $manager): Response
    {
       
        if (empty($this->cartService->getFullCart())) {
            return $this->redirectToRoute('welcome');
        }
        $cus =  $request->request->get('customer');
        $repo = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repo->findBy(['phone' => $cus]); 
        foreach ($customer as $key) {
            $customer = $key;
        }    
        // die();
        // $repo = $this->getDoctrine()->getRepository(Customer::class);
        if ($customer == null) {
            $customer = new Customer(); 
        }
        $form = $this->createForm(CustomerFormType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $repo = $this->getDoctrine()->getRepository(Commune::class);
            $com = $repo->find($request->request->get('commune'));
            /* dd($com ); */
           /*  $em = $manager->getManager();
            $em->persist($customer);
            $em->flush(); */
            $this->cartService->ConfirmeDataCustomer($customer);
            $ordered = new Ordered(); 
            $ordered->setCommune($com);
            $ordered->setQuantity(1);
            if ($com->getMontantMax() <=  $this->cartService->getTotale()) {
                $ordered->setTotalOrdered($this->cartService->getTotale());
            }else{
                $ordered->setTotalOrdered($this->cartService->getTotale() +  $com->getTarif());
            }
            $ordered->setCreatedAt(new \DateTime());
            $ordered->setNumeroOrder("EM"."".strval(rand(1,1000)+1));
            $ordered->setStatusOrdered(-1);
            $ordered->setCustomer($customer);
            
            /* $em = $manager->getManager();
            $em->persist($ordered);
            $em->flush(); */
            $this->cartService->ConfirmeDataOrdered($ordered);
            $panier = $this->cartService->getFullCart();
            foreach ($panier as $key) {
                $orderedDetail = new OrderedDetail(); 
                $orderedDetail->setQuantity($key['quantity']);
                $orderedDetail->setDateOrder(new \DateTime());
                $orderedDetail->setProduct($key['product']);
                $orderedDetail->setOrdered($ordered);
               /*  $em = $manager->getManager();
                $em->persist($orderedDetail);
                $em->flush(); */
                /* if ($orderedDetail) {
                    $this->cartService->remove($key['product']->getId());
                } */
            }
          
            // $message = (new \Swift_Message('Votre commande' ."  ".$ordered->getNumeroOrder()))
            // // les expediteur celui qui remplir le formulaire ou commandeur de produit 
            // ->setFrom($customer->getEmail())
            // ->setTo('libanehassan23@gmail.com')
            // ->setBody(
            //     $this->render('email/email.html.twig', compact('customer')),
            //     'text/html'
            // );
            // $mailer->send($message);
            // $this->mailerService->sendEmail('libanehassan23@gmail.com',$customer->getEmail(),  $this->render('email/email.html.twig', compact('customer')),
            //      'text/html');
            $this->addFlash('message', 'success', 'Votre commande à été bien enregistre! veuillez verifier votre mail');
            // Une fois que vous aurez la connexion active la ligne 151 pour envoi un mail au services clientele pour traiatement la commande avec le numero de la commande et le nom du client avec son numero de telephone 
            // $this->mailerService->sendEmailCustomer($customer->getEmail());
            return $this->redirectToRoute('confirm-ordered');
        }

        return $this->render('ordered/index.html.twig', [
            'controller_name' => 'OrderedController',
            'categories' =>  $categories = $this->categoryRepository->findAll(),
            'items' => $this->cartService->getFullCart(),
            'communs' => $communs = $this->communeRepository->findAll(),
            'form' => $form->createView(),  
            'Totale' => $this->cartService->getTotale()
        ]);
    }

    /**
     * @Route("/orderedConfirm", name="confirm-ordered")
    */
    public function confirmOrdered()
    {
        return $this->render('ordered/confirmeOrdered.html.twig', [
            'controller_name' => 'OrderedController',
            'categories' =>  $categories = $this->categoryRepository->findAll(),
            'items' => $this->cartService->getFullCart(),
            'ordered' => $this->cartService->getDataConfirmOrdered(),
            'Totale' => $this->cartService->getTotale()
        ]);
    }

    /**
     * @Route("/orderedSave", name="save-ordered")
    */
    public function saveOrdered(ManagerRegistry $manager)
    {
        $cle = "+00253";
       $sms = new SmsMessage(
            $cle.'77830971',
            'Send Sms from my app'
       );
     //  dd($sms);
     //  $sendMessage= $texterInterface->send($sms);
       $getordered = $this->cartService->getDataConfirmOrdered();
       $customer = $getordered->getCustomer();
    //   $this->mailerService->sendEmailCustomer($customer->getEmail());
       $em = $manager->getManager();
       $em->persist($customer);
       $em->flush();
       $repo = $this->getDoctrine()->getRepository(Commune::class);
       $com = $repo->find($getordered->getCommune()->getId());
       $ordered = new Ordered();
       $ordered->setCommune($com);
       $ordered->setCustomer($getordered->getCustomer());
       $ordered->setNumeroOrder($getordered->getNumeroOrder());
       $ordered->setQuantity($getordered->getQuantity());
       $ordered->setCreatedAt($getordered->getCreatedAt());
       $ordered->setStatusOrdered($getordered->getStatusOrdered());
       $ordered->setTotalOrdered($this->cartService->getTotale() +  $getordered->getCommune()->getTarif());
       $em = $manager->getManager();
       $em->persist($ordered);
       $em->flush();
       $panier = $this->cartService->getFullCart();
       foreach ($panier as $key) {
           $orderedDetail = new OrderedDetail(); 
           $orderedDetail->setQuantity($key['quantity']);
           $orderedDetail->setDateOrder(new \DateTime());
           $orderedDetail->setProduct($key['product']);
           $orderedDetail->setOrdered($ordered);
            $em = $manager->getManager();
           $em->persist($orderedDetail);
           $em->flush(); 
            if ($orderedDetail) {
               $this->cartService->remove($key['product']->getId());
           } 
       }
      // $this->mailerService->sendEmailFourniseur($customer->getPhone(),$customer->getName(), $ordered->getNumeroOrder());
       return $this->redirectToRoute('confirm-ordered');
    }

    /**
     * @Route("/ordered/{id}/livraison", name="runlivraison")
    */
    public function runlivraison($id, ManagerRegistry $manager)
    {
       $repo = $this->getDoctrine()->getRepository(Ordered::class);
       $ordered = $repo->find($id);
       $livraison = new Livraison();
       $livraison->setDatelivraison(new \DateTime());
       $livraison->setOrdered($ordered);
       $em = $manager->getManager();
       $em->persist($livraison);
       $em->flush();
       return $this->redirectToRoute('ordereds');
    }
    /**
     * @Route("/ordered/{ordered}/ordered", name="getOrderedbyProducts")
    */
    public function getOrderedbyProduct(ManagerRegistry $manager, Ordered $ordered, ProductRepository $productRepository)
    {
        $products = $manager->getRepository(OrderedDetail::class)->findBy(['ordered' => $ordered]);
        return null; 
    }
    // private function sendEmail($expediteur, $msg, $numOrdered, \Swift_Mailer $mailer)
    // {
    //     $message = (new \Swift_Message($msg ."  ".$numOrdered))
    //     // les expediteur celui qui remplir le formulaire ou commandeur de produit 
    //     ->setFrom($expediteur)
    //     ->setTo('libanehassan23@gmail.com')
    //     ->setBody(
    //         $this->render('email/email.html.twig'),
    //         'text/html'
    //     );
    //     $mailer->send($message);
    //     $this->addFlash('message', 'success', 'Votre commande à été bien enregistre! veuillez verifier votre mail');
    //     return $this->redirect('/','votre commande a ',200);
    // }

    private function sendEmailCustomer($to)
    {
        $email = (new Email())
            ->from('libanehassan23@gmail.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Djib-Shop')
            ->text('Votre commande à été bien enregistre !');
            $this->mailer->send($email);
    }

    private function sendEmailNotificationServiceOrderedForShop($to, $phoneCustomer, $nameCustomer, $numOrdered)
    {
        # code...
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ordereds/{id}/play", name="orderedsPlay", methods={"POST","GET"})
     */
    public function play(int $id, Ordered $ordered, ManagerRegistry $manager)
    {
        
        $repo = $this->getDoctrine()->getRepository(Ordered::class);
        $orderedDetail = $repo->find($id);
      //  dd($orderedDetail);
        $orderedDetail->setStatusOrdered(0);
        $em = $manager->getManager();
        $em->persist($orderedDetail);
        $em->flush();
        return $this->redirectToRoute('ordereds');
    }
    
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ordereds/{id}/pause", name="orderedsPause", methods={"POST","GET"})
     */
    public function pause(int $id, Ordered $ordered, ManagerRegistry $manager)
    {
        
        $repo = $this->getDoctrine()->getRepository(Ordered::class);
        $orderedDetail = $repo->find($id);
      //  dd($orderedDetail);
        $orderedDetail->setStatusOrdered(1);
        $em = $manager->getManager();
        $em->persist($orderedDetail);
        $em->flush();
        return $this->redirectToRoute('ordereds');
    }

  


    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ordereds/{id}/generate_pdf", name="generate_pdf", methods={"POST","GET"})
     */
    public function generate_pdf(int $id,Ordered $ordered){
    
        $options = new Options();
        $options->set('defaultFont', 'Roboto');
        
       
        $dompdf = new Dompdf($options);
        
        $data = array(
            'headline' => 'La Facture de la commande'
        );
        $repo = $this->getDoctrine()->getRepository(OrderedDetail::class);
        $orderedDetail = $repo->findBy(['ordered' => $id]);
        $html = $this->renderView('ordered/pdf.html.twig', [
        'orderedDetail' => $orderedDetail, 
        'categories' =>  $this->categoryRepository->findAll(),
        'items' => $this->cartService->getFullCart(),
        'ordered' => $ordered
        ]);
           
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("testpdf.pdf", [
            "Attachment" => true
        ]);
    }
}