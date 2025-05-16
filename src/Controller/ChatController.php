<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        $apiKey = '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';
        $referer = 'https://www.sitename.com';
        $siteName = 'SiteName';

        return $this->render('chat/chat.html.twig', [
            'api_key' => $apiKey,
            'referer' => $referer,
            'site_name' => $siteName,
        ]);
    }
}
