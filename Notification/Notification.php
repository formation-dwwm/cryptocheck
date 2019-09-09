<?php

namespace App\Notification;

use App\Entity\User;
use Twig\Environment;

class UserNotification{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer){

        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function forgotPassword(User $user){
        
        
    }
}