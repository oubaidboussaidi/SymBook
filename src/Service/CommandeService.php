<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack; // Utilisation de RequestStack
use App\Repository\LivresRepository;

class CommandeService
{
    private EntityManagerInterface $em;
    private LivresRepository $livresRepo;
    private $session;


    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, LivresRepository $livresRepo)
    {
        $this->em = $em;
        $this->livresRepo = $livresRepo;
        $this->session = $requestStack->getSession();
    }

    public function createCommande(User $user): Commande
    {
        $cart = $this->session->get('cart', []);

        $commande = new Commande();
        $commande->setUser($user);
        $commande->setDateCommande(new \DateTime());
        $commande->setStatut('en_attente');

        $total = 0;

        foreach ($cart as $livreId => $quantite) {
            $livre = $this->livresRepo->find($livreId);
            if (!$livre) continue;

            $ligne = new LigneCommande();
            $ligne->setLivre($livre);
            $ligne->setQuantite($quantite);
            $ligne->setPrixUnitaire($livre->getPrix());
            $ligne->setCommande($commande);

            $this->em->persist($ligne);

            $total += $livre->getPrix() * $quantite;
        }

        $commande->setTotal($total);
        $this->em->persist($commande);
        $this->em->flush();

        $this->session->remove('cart');

        return $commande;
    }
}
