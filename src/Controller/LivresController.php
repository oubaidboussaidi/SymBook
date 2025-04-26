<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivresController extends AbstractController
{
    #[Route('/livres/show/{id}', name: 'app_livres_show')]
    public function show(Livres $livre): Response
    {
        return $this->render('livres/rechercher.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/livres/show/title/{titre}', name: 'app_livres_show_by_title')]
    public function showByTitle(LivresRepository $rep, string $titre): Response
    {
        $livre = $rep->findOneBy(['titre' => $titre]);

        if (!$livre) {
            throw $this->createNotFoundException('Le livre avec ce titre n\'existe pas');
        }

        dd($livre);
    }

    #[Route('/livres/show/prix', name: 'app_livres_show_by_prix')]
    public function showByPrixDesc(LivresRepository $rep): Response
    {
        $livres = $rep->findBy([], ['prix' => 'DESC']);

        if (!$livres) {
            throw $this->createNotFoundException('Aucun livre trouvé');
        }

        dd($livres);
    }

    #[Route('/livres/list', name: 'app_livres_list')]
    public function listAll(LivresRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $repository->createQueryBuilder('l');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('livres/listAll.html.twig', ['livres' => $pagination]);
    }

    #[Route('/livres/create', name: 'app_livres_create')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $livre = new Livres();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();
            $this->addFlash('success', "Le livre {$livre->getTitre()} a été enregistré");
            return $this->redirectToRoute('app_livres_list');
        }

        return $this->render('livres/create.html.twig', ['f' => $form]);
    }

    #[Route('/livres/delete/{id}', name: 'app_livres_delete')]
    public function delete(Livres $livre, EntityManagerInterface $em): Response
    {
        $em->remove($livre);
        $em->flush();
        $this->addFlash('success', "Le livre  a ete supprime");
        return $this->redirectToRoute('app_livres_list');
    }

    #[Route('/livres/edit/{id}', name: 'app_livres_edit')]
    public function edit(Livres $livre, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le livre a été mis à jour');
            return $this->redirectToRoute('app_livres_list');
        }

        return $this->render('livres/edit.html.twig', ['f' => $form]);
    }

    #[Route('/livres/all', name: 'app_livres_all')]
    public function all(LivresRepository $rep, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $rep->createQueryBuilder('l');

        $livres = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('livres/all.html.twig', ['livre' => $livres]);
    }
}
