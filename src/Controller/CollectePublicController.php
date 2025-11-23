<?php

namespace App\Controller;

use App\Entity\Collecte;
use App\Repository\CollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CollectePublicController extends AbstractController
{
    #[Route('/collectes', name: 'app_collecte_public_index')]
    public function index(CollecteRepository $collecteRepository): Response
    {
        $collectes = $collecteRepository->findBy(['statut' => 'Planifiée'], ['dateDebut' => 'ASC']);

        return $this->render('collecte_public/index.html.twig', [
            'collectes' => $collectes,
        ]);
    }

    #[Route('/collectes/{id}', name: 'app_collecte_public_show')]
    public function show(Collecte $collecte): Response
    {
        return $this->render('collecte_public/show.html.twig', [
            'collecte' => $collecte,
        ]);
    }
}