<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Repository\ClientRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientController extends AbstractController
{

    /**
     * @Route("/client/attente", name="app_client_attente")
     */
    public function enAttente(Request $request, ClientRepository $clientRepository): Response
    {
        
        return $this->render('client/waiting.html.twig', [
            'items' => $clientRepository->findBy(array('status' => "En attente")),
        ]);
    }

    /**
     * @Route("client/reserv/confirm/{slug}", name="app_client_paiement",methods={"POST","GET"})
     */
    public function payer($slug, Request $request, EntityManagerInterface $em, ClientRepository $clientRepository): Response
    {

        
        $split = explode('#', $slug);
        $id = intval($split[1]);
        $client = $clientRepository->find($id);

        if ($_POST) {
            $client->setStatus("Confirmer");
            $em->flush();
            $paiement = new Paiement();
            $paiement->setClient($client)
                ->setUser($this->getUser())
                ->setMontant($client->getMontant())
                ->setCreatedAt(new DateTimeImmutable());
            $em->persist($paiement);
            $em->flush();
            $this->addFlash('success', 'Element mis a jour.');
            return $this->redirectToRoute('app_client_attente');
        }
        return $this->render(
            'client/form.html.twig',
            array(
                'client' => $client,
                'action' => "Enregistrement",
                'label' => "Payeiement"
            )
        );
    }

    /**
     * @Route("/client/confirm", name="app_client_confirmer")
     */
    public function confirm(Request $request, ClientRepository $clientRepository): Response
    {
       
        return $this->render('client/confirm.html.twig', [
            'items' => $clientRepository->findBy(array('status' => "Confirmer")),

        ]);
    }
}
