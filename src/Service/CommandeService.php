<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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
    if (empty($cart)) {
        throw new \Exception("Le panier est vide.");
    }

    $commande = new Commande();
    $commande->setUser($user);
    $commande->setDateCommande(new \DateTime());
    $commande->setStatut('en_attente');

    $total = 0;
    $hasValidItems = false;

    foreach ($cart as $livreId => $quantite) {
        $livre = $this->livresRepo->find($livreId);
        if (!$livre) continue;

        if ($quantite > $livre->getQuantiteDisponible()) {
            continue;
        }

        $hasValidItems = true;

        $ligne = new LigneCommande();
        $ligne->setLivre($livre);
        $ligne->setQuantite($quantite);
        $ligne->setPrixUnitaire($livre->getPrix());
        $ligne->setCommande($commande);

        $this->em->persist($ligne);

        $livre->setQuantiteDisponible($livre->getQuantiteDisponible() - $quantite);
        $this->em->persist($livre);

        $total += $livre->getPrix() * $quantite;
    }

    if (!$hasValidItems || $total == 0) {
        throw new \Exception("Commande invalide : aucun article disponible ou montant total nul.");
    }

    $commande->setTotal($total);
    $this->em->persist($commande);
    $this->em->flush();

    $this->session->remove('cart');

    return $commande;
}

}
