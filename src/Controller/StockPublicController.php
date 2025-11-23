<?php

namespace App\Controller;

use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockPublicController extends AbstractController
{
    #[Route('/stocks', name: 'app_stock_public_index')]
    public function index(StockRepository $stockRepository): Response
    {
        $stocks = $stockRepository->findAll();

        return $this->render('stock_public/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
}