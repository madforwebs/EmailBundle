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


use Buzinger\EmailingBundle\Entity\Message;

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
            return 'bigmamaswingtest@gmail.com';
        } else {
            return $destiny;
        }
    }

    public function sendMessage($data)
    {
        $data['origin'] = 'info@bigmamaswing.com';

        /** @var Message $mail */
        $mail = $this->em->getRepository('BuzingerEmailingBundle:Message')->findOneBy(['name' => $data['messageTemplateName']]);

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
            $body = str_replace('#firstNameUser#',ucfirst($data['name']), $body);
        }

        if(isset($data['link'])){
            $body = str_replace('#link#',$data['link'], $body);
        }
        if(isset($data['link'])){
            $body = str_replace('#link#',$data['link'], $body);
        }

        if(isset($data['link2'])){
            $body = str_replace('#link2#',$data['link2'], $body);
        }
        if(isset($data['link2'])){
            $body = str_replace('#link2#',$data['link2'], $body);
        }

        if(isset($data['course'])){
            if(isset($data['language']))
            {
                $translations = $data['course']->getTranslations($this->em);
                if(isset($translations[$data['language']]))
                {
                    $body = str_replace('#course#',$translations[$data['language']]['name'], $body);
                }else{
                    $localeTmp = $data['course']->getLocale();
                    $data['course']->setLocale('es');
                    $this->em->refresh($data['course']);

                    $body = str_replace('#course#',$data['course']->getName(), $body);
                    $data['course']->setLocale($localeTmp);
                    $this->em->refresh($data['course']);
                }
            }else{
                $localeTmp = $data['course']->getLocale();
                $data['course']->setLocale('es');
                $this->em->refresh($data['course']);

                $body = str_replace('#course#',$data['course']->getName(), $body);
                $data['course']->setLocale($localeTmp);
                $this->em->refresh($data['course']);
            }

        }

        if(isset($data['couple'])){
            $body = str_replace('#couple#',$data['couple'], $body);
        }

        if(isset($data['rolDance'])){
            $body = str_replace('#rol#',$data['rolDance'], $body);
        }

        if(isset($data['rolCouple'])){
            $body = str_replace('#rolCouple#',$data['rolCouple'], $body);
        }

        if(isset($data['friend'])){
            $body = str_replace('#firstNameCouple#',$data['friend'], $body);
        }

        if(isset($data['schedule'])){
            $body = str_replace('#schedule#',$data['schedule'], $body);
        }

        if(isset($data['pendingLevel'])){
            $body = str_replace('#pendingLevel#',$data['pendingLevel'], $body);
        }

        if(isset($data['nextPayment'])){
            $body = str_replace('#nextPayment#',$data['nextPayment'], $body);
        }

        if(isset($data['sesson'])){
            $body = str_replace('#sesson#',$data['sesson'], $body);
        }

        if(isset($data['leftDays'])){
            $body = str_replace('#leftDays#',$data['leftDays'], $body);
        }

        if(isset($data['subject'])){
            $body = str_replace('#subjectContact#',$data['subject'], $body);
        }

        if(isset($data['message'])){
            $body = str_replace('#messageContact#',$data['message'], $body);
        }

        if(isset($data['emailContact'])){
            $body = str_replace('#emailContact#',$data['emailContact'], $body);
        }

        if(isset($data['payments'])){
            $body = str_replace('#payments#',$data['payments'], $body);
        }
        if(isset($data['payments'])){
            $body = str_replace('#paymentsList#',$data['payments'], $body);
        }

        if(isset($data['payments'])){
            $body = str_replace('#listPaymentsCourse#',$data['payments'], $body);
        }

        if(isset($data['inscription'])){
            $body = str_replace('#inscription#',$data['inscription'], $body);
        }
        if(isset($data['coupleEmail'])){
            $body = str_replace('#coupleEmail#',$data['coupleEmail'], $body);
        }

        if(isset($data['dateSession'])){
            $body = str_replace('#dateSession#',ucfirst($data['dateSession']), $body);
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
