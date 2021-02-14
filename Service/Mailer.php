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

//require  __DIR__ . '/../../../../vendor/autoload.php';

use MadForWebs\EmailBundle\Entity\Template;
//use SendGrid;
//use sfConfig;
use Symfony\Component\Yaml\Yaml;

/**
 * Genera directivas SSI de include virtual
 * http://en.wikipedia.org/wiki/Server_Side_Includes.
 */
class Mailer
{
    /**
     * @var EntityManager
     */
    protected $em;

    private $router;

    private $memcached;

    private $translator;

    public function __construct($entityManager, $router, $translator, $templating, $mailer)
    {
        $this->em = $entityManager;
        $this->router = $router;
        $this->translator = $translator;
        $this->templating = $templating;
        $this->mailer = $mailer;

        return $this;
    }

    public function sendMessage($destiny, $origin, $subject, $messageTmp, $templateName = null , $files = null )
    {
        $em = $this->em;
        $rootDir = __DIR__.'/../../../../app/config/parameters.yml';

        $value = Yaml::parse(file_get_contents($rootDir));
        $bcc = $value['parameters']['mailer_bcc'];
        $enviroment = $value['parameters']['enviroment'];
        $sendEmail = $value['parameters']['sendEmail'];

        if (null == $templateName) {
            $templateName = $value['parameters']['default_template_name'];
        }

        /** @var \MadForWebs\EmailBundle\Entity\Template $mail */
        $template = $em->getRepository('EmailBundle:Template')->findOneBy(['name' => $templateName]);
        $subject = $this->translator->trans($subject);
        $value = Yaml::parse(file_get_contents($rootDir));
        $bcc = $value['parameters']['mailer_bcc'];
        $enviroment = $value['parameters']['enviroment'];
        $sendEmail = $value['parameters']['sendEmail'];

        if ('dev' == $enviroment) {
            $subject = '[DEV] '.$subject;
        }
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($origin);
        $templateComplete = str_replace('#content#', $messageTmp, $template->getContent());


        if (null != $files){
            foreach ($context['ficheros'] as $fichero)
                $message->attach(\Swift_Attachment::fromPath($fichero));
        }

        $message->setBody($templateComplete, 'text/html');
        $message->setTo(trim($destiny));
        if (true) {
            $this->mailer->send($message);
        }
    }
}
