<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Form\AffectationFilterType;
use App\Form\AffectationType;
use App\Repository\AffectationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/affectation')]
class AffectationController extends AbstractController
{
    #[Route('', name: 'app_affectation_index', methods: ['GET'])]
    public function index(Request $request, AffectationRepository $repo): Response
    {
        $filterForm = $this->createForm(AffectationFilterType::class);
        $filterForm->handleRequest($request);
        $data = $filterForm->isSubmitted() && $filterForm->isValid() ? $filterForm->getData() : [];

        $affectations = $repo->findByFilters(
            $data['fonction'] ?? null,
            $data['dateDebut'] ?? null,
            $data['dateFin'] ?? null,
            $data['ville'] ?? null,
        );

        return $this->render('affectation/index.html.twig', [
            'affectations' => $affectations,
            'filterForm' => $filterForm,
        ]);
    }

    #[Route('/new', name: 'app_affectation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $affectation = new Affectation();
        $form = $this->createForm(AffectationType::class, $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($affectation);
            $em->flush();
            $this->addFlash('success', 'Affectation créée.');
            return $this->redirectToRoute('app_affectation_index');
        }

        return $this->render('affectation/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_affectation_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Affectation $affectation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AffectationType::class, $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Affectation modifiée.');
            return $this->redirectToRoute('app_collaborateur_show', [
                'id' => $affectation->getCollaborateur()->getId(),
            ]);
        }

        return $this->render('affectation/edit.html.twig', [
            'form' => $form,
            'affectation' => $affectation,
        ]);
    }
}
