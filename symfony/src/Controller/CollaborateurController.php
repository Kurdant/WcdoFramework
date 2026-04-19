<?php

namespace App\Controller;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Form\AffectationType;
use App\Form\CollaborateurFilterType;
use App\Form\CollaborateurType;
use App\Repository\AffectationRepository;
use App\Repository\CollaborateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/collaborateur')]
class CollaborateurController extends AbstractController
{
    #[Route('', name: 'app_collaborateur_index', methods: ['GET'])]
    public function index(Request $request, CollaborateurRepository $repo): Response
    {
        $filterForm = $this->createForm(CollaborateurFilterType::class);
        $filterForm->handleRequest($request);
        $data = $filterForm->isSubmitted() && $filterForm->isValid() ? $filterForm->getData() : [];

        $collaborateurs = $repo->findByFilters(
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['email'] ?? null,
        );

        return $this->render('collaborateur/index.html.twig', [
            'collaborateurs' => $collaborateurs,
            'filterForm' => $filterForm,
            'mode' => 'all',
        ]);
    }

    #[Route('/non-affectes', name: 'app_collaborateur_non_affectes', methods: ['GET'])]
    public function nonAffectes(CollaborateurRepository $repo): Response
    {
        return $this->render('collaborateur/index.html.twig', [
            'collaborateurs' => $repo->findNonAffectes(),
            'filterForm' => $this->createForm(CollaborateurFilterType::class)->createView(),
            'mode' => 'non_affectes',
        ]);
    }

    #[Route('/new', name: 'app_collaborateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $collaborateur = new Collaborateur();
        $form = $this->createForm(CollaborateurType::class, $collaborateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncPassword($form, $collaborateur, $hasher);
            $em->persist($collaborateur);
            $em->flush();
            $this->addFlash('success', 'Collaborateur créé.');
            return $this->redirectToRoute('app_collaborateur_show', ['id' => $collaborateur->getId()]);
        }

        return $this->render('collaborateur/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_collaborateur_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Collaborateur $collaborateur, AffectationRepository $affectationRepo): Response
    {
        return $this->render('collaborateur/show.html.twig', [
            'collaborateur' => $collaborateur,
            'actives' => $affectationRepo->findActivesByCollaborateur($collaborateur),
            'historique' => $affectationRepo->findAllByCollaborateur($collaborateur, null, null),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collaborateur_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        Collaborateur $collaborateur,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        AffectationRepository $affectationRepo,
    ): Response {
        $form = $this->createForm(CollaborateurType::class, $collaborateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncPassword($form, $collaborateur, $hasher);
            $em->flush();
            $this->addFlash('success', 'Collaborateur modifié.');
            return $this->redirectToRoute('app_collaborateur_show', ['id' => $collaborateur->getId()]);
        }

        return $this->render('collaborateur/edit.html.twig', [
            'form' => $form,
            'collaborateur' => $collaborateur,
            'actives' => $affectationRepo->findActivesByCollaborateur($collaborateur),
        ]);
    }

    #[Route('/{id}/affecter', name: 'app_collaborateur_affecter', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function affecter(Request $request, Collaborateur $collaborateur, EntityManagerInterface $em): Response
    {
        $affectation = (new Affectation())->setCollaborateur($collaborateur);
        $form = $this->createForm(AffectationType::class, $affectation, ['lock_collaborateur' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($affectation);
            $em->flush();
            $this->addFlash('success', 'Affectation créée.');
            return $this->redirectToRoute('app_collaborateur_show', ['id' => $collaborateur->getId()]);
        }

        return $this->render('collaborateur/affecter.html.twig', [
            'form' => $form,
            'collaborateur' => $collaborateur,
        ]);
    }

    private function syncPassword($form, Collaborateur $collaborateur, UserPasswordHasherInterface $hasher): void
    {
        $plain = $form->get('plainPassword')->getData();
        if ($collaborateur->isAdministrateur()) {
            if ($plain !== null && $plain !== '') {
                $collaborateur->setMotDePasse($hasher->hashPassword($collaborateur, $plain));
            } elseif ($collaborateur->getMotDePasse() === null) {
                $form->get('plainPassword')->addError(new \Symfony\Component\Form\FormError(
                    "Un mot de passe est requis pour un administrateur."
                ));
            }
        } else {
            $collaborateur->setMotDePasse(null);
        }
    }
}
