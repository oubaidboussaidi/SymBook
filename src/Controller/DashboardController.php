<?php

namespace App\Controller;

use App\Repository\LigneCommandeRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        Request $request,
        LigneCommandeRepository $ligneCommandeRepo,
        CommandeRepository $commandeRepo
    ) {
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        $topBook = $ligneCommandeRepo->findMostSoldBook($startDate, $endDate);
        $ordersByDate = $commandeRepo->countOrdersByDate($startDate, $endDate);

        return $this->render('admin/dashboard.html.twig', [
            'topBook' => $topBook,
            'ordersByDate' => $ordersByDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
