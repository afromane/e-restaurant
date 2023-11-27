<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    /**
     * @Route("/pdf", name="app_pdf")
     */
    public function index(): Response
    {
        return $this->render('pdf/index.html.twig', [
            'controller_name' => 'PdfController',
        ]);
    }

    /**
     * @Route("/client/pdf/attente", name="app_client_attente_pdf")
     */
    public function enAttente(ClientRepository $clientRepository, $Attachment = false)
    {

        $pdfOptions = new Options();
        $pdfOptions->set(array(
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ));

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('pdf/waiting.html.twig', [
            'items' => $clientRepository->findBy(array('status'=>"En attente")),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("seance du_" . time() . ".pdf", [
            "Attachment" => $Attachment // true if we want to download automatically file
        ]);
        // return $this->redirectToRoute('app_vente');
    }
}
