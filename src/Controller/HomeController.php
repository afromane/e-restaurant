<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Client;
use DateTimeImmutable;
use App\Service\EmailService;
use Symfony\Component\Mime\Email;
use App\Repository\MenuRepository;
use App\Repository\RepasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * @Route("/send-email", name="send_email")
     */
    public function sendEmail(): Response
    {
       
        $subject = 'Test Email';
        $recipient = 'issamanel45@gmail.com';
        $body = 'This is a test email sent from Symfony';
       dd( $this->emailService->sendEmail($subject, $recipient, $body));

        return new Response(json_encode('tes'));
    }
    
    /**
     * @Route("/", name="app_home")
     */
    public function index(MenuRepository $menuRepository, RepasRepository $repasRepository): Response
    {

        return $this->render('home/index.html.twig', [
            'isHome' => true,
            'menu' => $menuRepository->findAll(),
            'repas' => $repasRepository->findAll()
        ]);
    }
    /**
     * @Route("/about", name="app_about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'isHome' => false,
        ]);
    }
    /**
     * @Route("/menu", name="app_home_menu")
     */
    public function menu(MenuRepository $menuRepository, RepasRepository $repasRepository): Response
    {

        return $this->render('home/menu.html.twig', [
            'isHome' => false,
            'menu' => $menuRepository->findAll(),
            'repas' => $repasRepository->findAll()
        ]);
    }
    /**
     * @Route("/reservation/", name="app_home_reservation",methods={"POST","GET"})
     */
    public function reservation(Request $request,EntityManagerInterface $em): Response
    {

        if ($_POST) {
            $client = new Client();
            $client->setNomPrenom($request->request->get('name'))
                ->setCreatedAt(new DateTimeImmutable())
                ->setStatus('En attente')
                ->setDateReserv(DateTime::createFromFormat("Y-m-d", $request->request->get('date')))
                ->setPhone($request->request->get('phone'))
                ->setPlace($request->request->get('people'))
                ->setMontant(2000)
                ->setEmail($request->request->get('email'));

                $em->persist($client);
                $em->flush();

                $pdfOptions = new Options();
                $pdfOptions->set(array(
                    'defaultFont' => 'Arial',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ));
        
                $dompdf = new Dompdf($pdfOptions);
                $html = $this->renderView('pdf/ticket.html.twig', [
                    'client'=>$client
                ]);
        
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A5', 'portrait');
                $dompdf->render();
                $dompdf->stream("ticket" . time() . ".pdf", [
                    "Attachment" => false // true if we want to download automatically file
                ]);

                return $this->redirectToRoute('app_home');


        }
        return $this->render('home/reservation.html.twig', [
            'isHome' => false,
        ]);
    }

   
}
