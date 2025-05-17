<?php

namespace App\Controller;

use App\Service\CommandeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CommandeRepository;

final class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/commande/valider', name: 'app_commande_valider')]
    #[IsGranted('ROLE_USER')]
    public function valider(CommandeService $commandeService): Response
    {
        $user = $this->getUser();

        try {
            $commande = $commandeService->createCommande($user);

            return $this->render('commande/success.html.twig', [
                'commande' => $commande
            ]);
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('cart_index');
        }
    }

    #[Route('/mes-commandes', name: 'app_commande_historique')]
    #[IsGranted('ROLE_USER')]
    public function historique(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $commandes = $commandeRepository->findByUser($user);

        return $this->render('commande/historique.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
