<?php

namespace App\Controller;

use App\Repository\CollecteRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CollecteRepository $collecteRepository, StockRepository $stockRepository): Response
    {
        // Récupère les 3 prochaines collectes (statut "Planifiée")
        $prochainesCollectes = $collecteRepository->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'Planifiée')
            ->orderBy('c.dateDebut', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // Récupère les stocks pour le baromètre
        $stocks = $stockRepository->findAll();

        return $this->render('home/index.html.twig', [
            'prochainesCollectes' => $prochainesCollectes,
            'stocks' => $stocks,
        ]);
    }
}