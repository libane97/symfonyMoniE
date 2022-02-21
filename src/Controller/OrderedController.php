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
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
            $em = $manager->getManager();
            $em->persist($customer);
            $em->flush();
           
            $ordered = new Ordered(); 
            $ordered->setCommune($com);
            $ordered->setQuantity(1);
            if ($com->getMontantMax() <=  $this->cartService->getTotale()) {
                $ordered->setTotalOrdered($this->cartService->getTotale());
            }else{
                $ordered->setTotalOrdered($this->cartService->getTotale() +  $com->getTarif());
            }
            $ordered->setCreatedAt(new \DateTime());
            $ordered->setNumeroOrder("EM"."".strval(rand(1,1000))."".$customer->getId());
            $ordered->setStatusOrdered(0);
            $ordered->setCustomer($customer);
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
            $this->mailerService->sendEmailCustomer($customer->getEmail());
            return $this->redirectToRoute('ordered');
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

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ordereds/{id}/play", name="orderedsPlay", methods={"POST","GET"})
     */
    public function play(int $id, Ordered $ordered, ManagerRegistry $manager)
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
     * @Route("/ordereds/{id}/pause", name="orderedsPause", methods={"POST","GET"})
     */
    public function pause(int $id, Ordered $ordered, ManagerRegistry $manager)
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
     * @Route("/ordereds/{id}/generate_pdf", name="generate_pdf", methods={"POST","GET"})
     */
    public function generate_pdf(int $id,Ordered $ordered){
    
        $options = new Options();
        $options->set('defaultFont', 'Roboto');
        
       
        $dompdf = new Dompdf($options);
        
        $data = array(
            'headline' => 'my headline'
        );
        $repo = $this->getDoctrine()->getRepository(OrderedDetail::class);
        $orderedDetail = $repo->findBy(['ordered' => $id]);
        $html = $this->renderView('ordered/show.html.twig', [
        'orderedDetail' => $orderedDetail, 
        'categories' =>  $categories = $this->categoryRepository->findAll(),
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