<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\MenuRepository;
use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function index(Request $request, MenuRepository $menuRepository, PaiementRepository $paiementRepository, ClientRepository $clientRepository): Response
    {
        $session = $request->getSession();
        $session->set('menu', $menuRepository->findAll());
        $session->set('roles', $this->getUser()->getRoles());

        $soldeReservByMois = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        foreach ($paiementRepository->findAll() as $value) {

            $split = explode('-', $value->getCreatedAt()->format('Y-m-d'));
            $soldeReservByMois[(int)$split[1] - 1] += $value->getClient()->getMontant();
        }

        // dd($session->get('menu'));
        return $this->render('dashboard/index.html.twig', [
            'confirm' => $paiementRepository->findAll(),
            'waiting' => $clientRepository->findBy(array('status' => "En attente")),
            'soldeReservByMois' => $soldeReservByMois
        ]);
    }
}
