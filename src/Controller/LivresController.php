<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Livres;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;

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
    public function listAll(LivresRepository $repository): Response
    {
        $livres = $repository->findAll();
        return $this->render('livres/listAll.html.twig', ['livres' => $livres]);
    }


//    #[Route('/livres', name: 'app_livres')]
  //  public function create(EntityManagerInterface $em): Response
    //{
      //  $livre = new Livres();
        //$livre->setTitre('titre 1');
       // $livre->setSlag('titre-1');
        //$livre->setImage('image1.jpg');
       // $livre->setResume('resume 1');
        //$livre->setEditeur('editeur 1');
        //$livre->setDateEdition(new \DateTime('2021-01-01'));
       // $livre->setPrix(25.99); // Added price field

        //$em->persist($livre);
        //$em->flush();

        //return new Response('Livre enregistré avec id ' . $livre->getId());
   // }
   
    #[Route('/livres/delete/{id}', name: 'app_livres_delete')]
public function delete(Livres $livre, EntityManagerInterface $em): Response 
{
    $em->remove($livre);
    $em->flush();
    return $this->redirectToRoute('app_livres_list');
}

}
