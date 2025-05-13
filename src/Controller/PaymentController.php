<?php
namespace App\Controller;

use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\StripeService;

class PaymentController extends AbstractController
{
    private $stripeService;
    private $mailer;

    public function __construct(StripeService $stripeService, MailerInterface $mailer)
    {
        $this->stripeService = $stripeService;
        $this->mailer = $mailer;
    }

    #[Route('/payment', name: 'payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/choose-payment', name: 'choose_payment')]
    public function choosePayment(): Response
    {
        return $this->render('payment/select.html.twig');
    }

    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(Request $request, CartService $cartService): Response
    {
        $paymentMethod = $request->get('payment_method');

        if ($paymentMethod === 'card') {

            Stripe::setApiKey($this->stripeService->getSecretKey());

            $cartItems = $cartService->getFullCart();
            $totalAmount = $cartService->getTotal() * 100;

            $lineItems = [];
            foreach ($cartItems as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => $item['livre']->getTitre(),
                        ],
                        'unit_amount'  => $item['livre']->getPrix() * 100,
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            return $this->redirect($session->url, 303);
        } elseif ($paymentMethod === 'delivery') {

            return $this->redirectToRoute('success_url');
        }

        return $this->redirectToRoute('choose_payment');
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(CartService $cartService): Response
    {
        $cartItems = $cartService->getFullCart();
        $totalAmount = $cartService->getTotal();

        $email = (new Email())
            ->from('no-reply@votre-site.com')
            ->to('client@example.com')
            ->subject('Confirmation de votre commande')
            ->html($this->renderView('payment/confirmation.html.twig', [
                'cartItems' => $cartItems,
                'totalAmount' => $totalAmount,
            ]));

        $this->mailer->send($email);

        return $this->render('payment/success.html.twig', []);
    }

    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }
}
