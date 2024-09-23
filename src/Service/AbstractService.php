<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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