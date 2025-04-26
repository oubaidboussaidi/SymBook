<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategorieType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoriesController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin_categories')]
    public function index(CategoriesRepository $rep): Response
    {   $categories=$rep->findAll();
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/admin/categories/create', name: 'admin_categories_create')]
    public function create(EntityManagerInterface $em,Request $request): Response
    {
        $category=new Categories();
        $form=$this->createForm(CategorieType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($category);
            $em->flush();
            $this->addFlash('success',"la cathegorie ".$category->getLibelle()." a ete enregistre");
            $this->addFlash('success',"un mail est envoye a l'admin");
            return $this->redirectToRoute('admin_categories');
        }


        return $this->render('categories/create.html.twig', ['f'=>$form]);
    }
    #[Route('/admin/categories/edit/{id}', name: 'admin_categories_edit')]
    public function edit(Categories $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success','la categorie a ete ajouter');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('categories/edit.html.twig', ['f'=>$form]);
    }

}
