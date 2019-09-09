<?php

namespace App\Services;

use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

/**
 * Class Mailer
 */
class Mailer
{
    private $renderer;
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->renderer = $renderer;
        $this->mailer = $mailer;
    }

    public function sendMessage(User $user, $attachement = null)
    {
        $mail = (new \Swift_Message($subject))
            ->setFrom('poupa_60@hotmail.fr')
            ->setTo('geoffroy.varlez@gmail.com')
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($user->getEmail())
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    public function createBodyMail($view, array $parameters)
    {
        return $this->renderer->render($view, $parameters);
    }
}