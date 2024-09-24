<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Custom Class abstraite pour une implémentation
 * simplifié des classes et dépendences du projet CodeXpress
 */
abstract class AbstractService
{
    public function __construct(
        protected ParameterBagInterface $parameter, 
        protected MailerInterface $mailer)
    {
        $this->parameter = $parameter;
        $this->mailer = $mailer;
    }
}