<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Repas;
use App\Repository\MenuRepository;
use App\Repository\RepasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\FileUploader;

class MenuController extends AbstractController
{
    /**
     * @Route("space/menu", name="app_menu")
     */
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'items' => $menuRepository->findAll(),
        ]);
    }
    /**
     * @Route("space/menu/new", name="app_menu_new",methods={"POST","GET"})
     */
    public function create(Request $request, EntityManagerInterface $em, MenuRepository $menuRepository): Response
    {
        if ($request->getSession()->get('isLogin') != true)
            // return $this->redirectToRoute('app_logout');
            $menu = new Menu();
        if ($_POST) {

            $menu->setLabel($request->request->get('label'));

            // $historique = new Historique();
            $session = $request->getSession();
            // $historique->setAction('insertion')
            //     ->setCreatedAt(new DateTimeImmutable())
            //     ->setEntite('type vente')
            //     ->setMembre($membreRepository->find($session->get('membreId')))
            //     ->setItem1((array)$types);

            // $em->persist($historique);

            $em->persist($menu);
            $em->flush();
            $this->addFlash('success', 'Element ajouter.');
            return $this->redirectToRoute('app_menu');
        }
        return $this->render(
            'menu/form.html.twig',
            array(
                'menu' => $menu,
                'action' => "Enregistrer",
                'label' => "Nouveau"
            )
        );
    }

    /**
     * @Route("space/menu/update/{id}", name="app_menu_update",methods={"POST","GET"})
     */
    public function editer($id, Request $request, EntityManagerInterface $em, MenuRepository $menuRepository): Response
    {
        if ($request->getSession()->get('isLogin') != true)
            // return $this->redirectToRoute('app_logout');
            $split = explode('#', $id);
        $id = intval($split[1]);
        $menu = new Menu();
        $menu = $menuRepository->find($id);
        if ($_POST) {
            $menu->setLabel($request->request->get('label'));

            // $historique = new Historique();
            // $session = $request->getSession();
            // $historique->setAction('edition')
            //     ->setCreatedAt(new DateTimeImmutable())
            //     ->setEntite('type vente')
            //     ->setMembre($membreRepository->find($session->get('membreId')))
            //     ->setItem1((array)$menu)
            //     ->setItem2((array)$old);

            // $em->persist($historique);
            $menu->setLabel($request->request->get('label'));
            $em->flush();
            $this->addFlash('success', 'Element mis a jour.');
            return $this->redirectToRoute('app_menu');
        }
        return $this->render(
            'menu/form.html.twig',
            array(
                'menu' => $menu,
                'action' => "Mettre a jour",
                'label' => "Mettre a jour"
            )
        );
    }

    /**
     * @Route("space/menu/repas/{id}", name="app_menu_repas",methods={"POST","GET"})
     */
    public function menuRepas($id, Request $request, EntityManagerInterface $em, MenuRepository $menuRepository, RepasRepository $repasRepository): Response
    {
        if ($request->getSession()->get('isLogin') != true)
            // return $this->redirectToRoute('app_logout');
            $split = explode('#', $id);
        $id = intval($split[1]);
        $menu = new Menu();
        $menu = $menuRepository->find($id);
        $repas = $repasRepository->findBy(array('menu' => $menu->getId()));


        return $this->render(
            'menu/repas.html.twig',
            array(
                'menu' => $menu,
                'repas' => $repas
            )
        );
    }

    /**
     * @Route("/repas/new/{id}", name="app_menu_new",methods={"POST","GET"})
     */
    public function nouveauRepas($id, Request $request, EntityManagerInterface $em, MenuRepository $menuRepository, FileUploader $fileUploader): Response
    {
        if ($request->getSession()->get('isLogin') != true)
            // return $this->redirectToRoute('app_logout');

            $split = explode('#', $id);
        $id = intval($split[1]);
        $menu = new Menu();
        $menu = $menuRepository->find($id);

        $repas = new Repas();
        if ($_POST) {
            /** @var UploadedFile $uploadFile */
            $file = $request->files->get('path');

            $sub = 'plat/';
            $filename = $sub . $fileUploader->upload($file, $sub);
            //$menu->setLabel($request->request->get('label'));
            // $em->persist($historique);
            $repas->setLabel($request->request->get('label'))
                ->setMenu($menu)
                ->setPath($filename)
                ->setDescription($request->request->get('description'))
                ->setPrix($request->request->get('prix'));

            $em->persist($repas);
            $em->flush();
            $this->addFlash('success', 'Element ajouter.');
            return $this->redirectToRoute('app_menu_repas',array(
                            'id'=>'0915_'.$menu->getId().''.$menu->getLabel().'#'.$menu->getID() )) ;
        }
        return $this->render(
            'menu/form-repas.html.twig',
            array(
                'menu' => $menu,
                'repas' => $repas,
                'action' => "Enregistrer",
                'label' => "Nouveau"
            )
        );
    }
}
