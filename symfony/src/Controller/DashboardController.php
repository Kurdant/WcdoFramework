<?php

namespace App\Controller;

use App\Repository\AffectationRepository;
use App\Repository\CollaborateurRepository;
use App\Repository\FonctionRepository;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        RestaurantRepository $restaurantRepo,
        CollaborateurRepository $collaborateurRepo,
        FonctionRepository $fonctionRepo,
        AffectationRepository $affectationRepo,
    ): Response {
        $stats = [
            'restaurants' => $restaurantRepo->count([]),
            'collaborateurs' => $collaborateurRepo->count([]),
            'fonctions' => $fonctionRepo->count([]),
            'affectations_actives' => $affectationRepo->count(['dateFin' => null]),
        ];

        return $this->render('dashboard/index.html.twig', ['stats' => $stats]);
    }
}
