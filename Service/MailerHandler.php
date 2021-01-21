<?php

/*
 * This file is part of the MadForWebs package
 *
 * Copyright (c) 2017 Fernando Sánchez Martínez
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Fernando Sánchez Martínez <fer@madforwebs.com>
 */

namespace MadForWebs\EmailBundle\Service;


use MadForWebs\EmailBundle\Entity\Message;

class MailerHandler
{
    protected $em;

    private $router;

    private $mailer;

    private $translator;

    protected $domain;

    protected $enviroment;

    public function __construct($entityManager, $router, $mailer, $translator, $domain, $enviroment)
    {
        $this->em = $entityManager;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->enviroment = $enviroment;

        return $this;
    }

    public function changeDestinyEnviroment($destiny)
    {
        $env = $this->enviroment;
        if ('dev' == $env) {
            return 'info@madforwebs.com';
        } else {
            return $destiny;
        }
    }

    public function sendMessage($data)
    {
        $data['origin'] = 'info@madforwebs.com';

        /** @var Message $mail */
        $mail = $this->em->getRepository('EmailBundle:Message')->findOneBy(['name' => $data['messageTemplateName']]);

        if(isset($data['language']))
        {
            $translations = $mail->getTranslations($this->em);
            if(isset($translations[$data['language']]))
            {
                $subject = $translations[$data['language']]['subject'];
                $body = $translations[$data['language']]['content'];
            }else{
                $mail->setLocale('es');
                $this->em->refresh($mail);

                $subject = $mail->getSubject();
                $body = $mail->getContent();
            }
        }else{
            $subject = $mail->getSubject();
            $body = $mail->getContent();
        }


        if(isset($data['name'])){
            $body = str_replace('#name#',ucfirst($data['name']), $body);
        }

        if(isset($data['origin'])){
            $body = str_replace('#origin#',ucfirst($data['origin']), $body);
        }

        $destiny = $this->changeDestinyEnviroment($data['email']);
        /** @var Mailer $mailer */
        $mailer = $this->mailer;
        $dataEmail = array();
        $dataEmail['subject'] = $subject;
        $dataEmail['body_text'] = $body;

        if (isset($data['template'])) {
            $dataEmail['template'] = $data['template'];
        } else {
            $dataEmail['template'] = null;
        }

        $mailer->sendMessage($destiny, $data['origin'], $subject, $body, $dataEmail['template']);
    }

    public function recoverUser($data)
    {
        $this->sendMessage($data);
    }

    public function sendConfirmationPayments($data)
    {
        $this->sendMessage($data);
    }

    public function newPassword($data)
    {
        $this->sendMessage($data);
    }

    public function newInscription($data)
    {
        $this->sendMessage($data);
    }

    public function newInscriptionCouple($data)
    {
        $this->sendMessage($data);
    }

    public function newInscriptionCoupleEmail($data)
    {
        $this->sendMessage($data);
    }

    public function noticeConfirmCouple($data)
    {
        $this->sendMessage($data);
    }

    public function noticeWaiting($data)
    {
        $this->sendMessage($data);
    }

    public function noticeExitWaiting($data)
    {
        $this->sendMessage($data);
    }

    public function noticeRejectCouple($data)
    {
        $this->sendMessage($data);
    }
}
