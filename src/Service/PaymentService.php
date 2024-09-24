<?php

namespace App\Service;

use Stripe\Stripe;
use App\Entity\Subscription;
use Stripe\Checkout\Session;
use App\Service\AbstractService;
use App\Repository\OfferRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentService 
{
    private $offer; // OFFRE PREMIUM
    private $domain; // NOM DE DOMAINE
    private $apiKey;

    public function __construct(
        private ParameterBagInterface $parameter,
        OfferRepository $or)
    {
        $this->parameter = $parameter;
        $this->offer = $or->findOneByName('Premium'); // Récupération de l'offre Premium
        $this->apiKey =$this->parameter->get('STRIPE_API_SK');
        $this->domain = 'https://127.0.0.1:8000'; // NOM DE DOMAINE
    }

    /**
     * askCheckout()
     * Méthode permettant de créer une session de paiement Stripe
     * @return Stripe\Checkout\Session
     */
    public function askCheckout(): ?Session
    {
        Stripe::setApiKey($this->apiKey); // Établissement de la connexion (requête API)        
        $checkoutSession = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $this->offer->getPrice() * 100, // Stripe utilise des centimes
                    'product_data' => [ // Les informations du produit sont personnalisables
                        'name' => $this->offer->getName(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->domain . '/payment-success',
            'cancel_url' => $this->domain . '/payment-cancel',
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);

        return $checkoutSession;
    }


    // Traitement du role des utilisateurs en fonction du paiement
    // Génération de la facture
    // Notifications email
}
