<?php

namespace App\Controller;

use App\Entity\Donateur;
use App\Entity\RendezVous;
use App\Entity\Don;
use App\Form\RendezVousType;
use App\Repository\CollecteRepository;
use App\Repository\RendezVousRepository;
use App\Repository\DonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DonateurController extends AbstractController
{
    #[Route('/donateur/dashboard', name: 'app_donateur_dashboard')]
    public function dashboard(RendezVousRepository $rendezVousRepository, DonRepository $donRepository): Response
    {
        /** @var Donateur $donateur */
        $donateur = $this->getUser();
        
        // Prochain rendez-vous
        $prochainRendezVous = $rendezVousRepository->findProchainRendezVous($donateur);
        
        // Calcul éligibilité
        $estEligible = $this->calculerEligibilite($donateur);
        $dateEligible = $this->calculerDateEligible($donateur);

        return $this->render('donateur/dashboard.html.twig', [
            'donateur' => $donateur,
            'prochain_rendez_vous' => $prochainRendezVous,
            'est_eligible' => $estEligible,
            'date_eligible' => $dateEligible,
        ]);
    }

    #[Route('/donateur/rdv/new/{collecteId}', name: 'app_donateur_rdv_new')]
    public function prendreRendezVous(Request $request, int $collecteId, CollecteRepository $collecteRepository, EntityManagerInterface $entityManager): Response
    {
        $collecte = $collecteRepository->find($collecteId);
        
        if (!$collecte) {
            throw $this->createNotFoundException('Collecte non trouvée');
        }

        $rendezVous = new RendezVous();
        $rendezVous->setDonateur($this->getUser());
        $rendezVous->setCollecte($collecte);
        $rendezVous->setStatut('Confirmé');

        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous confirmé avec succès!');
            return $this->redirectToRoute('app_donateur_dashboard');
        }

        return $this->render('donateur/prendre_rdv.html.twig', [
            'form' => $form->createView(),
            'collecte' => $collecte,
        ]);
    }

    #[Route('/donateur/historique', name: 'app_donateur_historique')]
    public function historique(DonRepository $donRepository): Response
    {
        /** @var Donateur $donateur */
        $donateur = $this->getUser();
        $dons = $donRepository->findBy(['donateur' => $donateur], ['dateDon' => 'DESC']);

        return $this->render('donateur/historique.html.twig', [
            'dons' => $dons,
            'donateur' => $donateur,
        ]);
    }

    #[Route('/donateur/rdv/{id}/annuler', name: 'app_donateur_rdv_annuler')]
    public function annulerRendezVous(RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        if ($rendezVous->getDonateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }

        $rendezVous->setStatut('Annulé');
        $entityManager->flush();

        $this->addFlash('success', 'Rendez-vous annulé avec succès!');
        return $this->redirectToRoute('app_donateur_dashboard');
    }

    private function calculerEligibilite(Donateur $donateur): bool
    {
        if (!$donateur->getDerniereDateDon()) {
            return true; // Premier don
        }

        $dernierDon = $donateur->getDerniereDateDon();
        $aujourdhui = new \DateTime();
        $difference = $dernierDon->diff($aujourdhui);

        // Éligible si 56 jours (8 semaines) depuis le dernier don
        return $difference->days >= 56;
    }

    private function calculerDateEligible(Donateur $donateur): ?\DateTime
    {
        if (!$donateur->getDerniereDateDon()) {
            return null;
        }

        $dateEligible = clone $donateur->getDerniereDateDon();
        $dateEligible->modify('+56 days'); // 8 semaines

        return $dateEligible;
    }
}