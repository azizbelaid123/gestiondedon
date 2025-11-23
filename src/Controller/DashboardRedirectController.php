<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardRedirectController extends AbstractController
{
    #[Route('/dashboard-redirect', name: 'app_dashboard_redirect')]
    public function index(): Response
    {
        // Rediriger selon le rôle
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin');
        }
        
        // Pour les donateurs
        return $this->redirectToRoute('app_donateur_dashboard');
    }
}