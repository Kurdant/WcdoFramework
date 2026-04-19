<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Entity\Restaurant;
use App\Form\AffectationType;
use App\Form\RestaurantFilterType;
use App\Form\RestaurantType;
use App\Repository\AffectationRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    #[Route('', name: 'app_restaurant_index', methods: ['GET'])]
    public function index(Request $request, RestaurantRepository $repo): Response
    {
        $filterForm = $this->createForm(RestaurantFilterType::class);
        $filterForm->handleRequest($request);
        $data = $filterForm->isSubmitted() && $filterForm->isValid() ? $filterForm->getData() : [];

        $restaurants = $repo->findByFilters(
            $data['nom'] ?? null,
            $data['codePostal'] ?? null,
            $data['ville'] ?? null,
        );

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
            'filterForm' => $filterForm,
        ]);
    }

    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($restaurant);
            $em->flush();
            $this->addFlash('success', 'Restaurant créé.');
            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_restaurant_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Request $request, Restaurant $restaurant, AffectationRepository $affectationRepo): Response
    {
        $fonction = $this->resolveFonction($request, 'fonction');
        $nom = $request->query->get('nom');
        $dateDebut = $this->resolveDate($request->query->get('dateDebut'));

        $affectations = $affectationRepo->findActivesByRestaurant($restaurant, $fonction, $nom, $dateDebut);

        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'affectations' => $affectations,
            'filters' => ['fonction' => $fonction, 'nom' => $nom, 'dateDebut' => $dateDebut],
        ]);
    }

    #[Route('/{id}/edit', name: 'app_restaurant_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $em, AffectationRepository $affectationRepo): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Restaurant modifié.');
            return $this->redirectToRoute('app_restaurant_show', ['id' => $restaurant->getId()]);
        }

        $fonction = $this->resolveFonction($request, 'fonction');
        $nom = $request->query->get('nom');
        $dateDebut = $this->resolveDate($request->query->get('dateDebut'));
        $historique = $affectationRepo->findAllByRestaurant($restaurant, $fonction, $nom, $dateDebut);

        return $this->render('restaurant/edit.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant,
            'historique' => $historique,
            'filters' => ['fonction' => $fonction, 'nom' => $nom, 'dateDebut' => $dateDebut],
        ]);
    }

    #[Route('/{id}/affecter', name: 'app_restaurant_affecter', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function affecter(Request $request, Restaurant $restaurant, EntityManagerInterface $em): Response
    {
        $affectation = (new Affectation())->setRestaurant($restaurant);
        $form = $this->createForm(AffectationType::class, $affectation, ['lock_restaurant' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($affectation);
            $em->flush();
            $this->addFlash('success', 'Collaborateur affecté.');
            return $this->redirectToRoute('app_restaurant_edit', ['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/affecter.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant,
        ]);
    }

    private function resolveFonction(Request $request, string $key): ?\App\Entity\Fonction
    {
        $id = $request->query->get($key);
        if ($id === null || $id === '') { return null; }
        /** @var \App\Repository\FonctionRepository $repo */
        $repo = $this->container->get('doctrine')->getRepository(\App\Entity\Fonction::class);
        return $repo->find((int) $id);
    }

    private function resolveDate(?string $value): ?\DateTimeInterface
    {
        if ($value === null || $value === '') { return null; }
        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }
}
