<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // 1. Récupérer l'utilisateur actuellement en ligne (pour connaitre son adresse)
        // Security
        /** @var User */
        $currentUser = $this->security->getUser();

        // 2. Récupérer la commande (je la trouverai dans PurchaseSuccessEvent)
        $purchase = $purchaseSuccessEvent->getPurchase();

        // 3. Ecrire le mail (nouveau TemplatedEmail)
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);

        // 5. Envoyer l'email 
        $this->mailer->send($email);

        // MailerInterface
        $this->logger->info("Email envoyé pour la commande n° " . $purchaseSuccessEvent->getPurchase()->getId());
    }
}
