<?php

namespace App\Traits;

use Exception;

trait EmailMessage
{
    /**
     * Email collection of messages by types.
     *
     * @var array
     */
    private $messages = [
        'link' => [
            'confirmation' => 'Cliquez sur le lien pour confirmer votre inscription',
            'reset' => 'Cliquez sur le lien pour changer votre mot de passe',
        ],
    ];

    /**
     * Swift mailer.
     *
     * @var
     */
    private $mailer;

    /**
     * Register a specified mailer.
     *
     * @param $mailer
     *
     * @return void
     */
    public function setMailer($mailer)
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport($_ENV['MAILER_SMTP'], $_ENV['MAILER_PORT'], $_ENV['MAILER_ENCRYPTION']))
        ->setUsername($_ENV['MAILER_USERNAME'])
        ->setPassword($_ENV['MAILER_PWD']);

        // stream options is required for localhost self signed certificates
        if ($transport instanceof \Swift_Transport_EsmtpTransport
                && isset($_ENV['APP_ENV']) && ('dev' == $_ENV['APP_ENV'] || 'test' == $_ENV['APP_ENV'])) {
            $transport->setStreamOptions([
                'ssl' => ['allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false, ],
                ]);
        }

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        $this->mailer = $mailer;
    }

    /**
     * Provide an email message with the coresponding type.
     *
     * @param string $type
     * @param string $messageId
     *
     * @return string
     */
    public function getEmailMessage($type, $messageId)
    {
        if (!array_key_exists($type, $this->messages)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid email type', $type));
        }

        if (!isset($this->messages[$type][$messageId])) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid email message', $messageId));
        }

        return $this->messages[$type][$messageId];
    }

    /**
     * Use a smtp server to send an html message. Swift mailer use a custom vps smtp server.
     *
     * @param $from
     * @param $to
     * @param $subject
     * @param $data
     * @param $type
     *
     * @return void
     */
    public function processMail($from, $to, $subject, $data, $type = null)
    {
        // dont send email with unit test : slow and security reason.
        if ('test' === $_ENV['APP_ENV'] || 'travis' === $_ENV['APP_ENV']) {
            return;
        }

        $view = 'emails/'.($type ? $type : 'link').'.html.twig';
        $message = (new \Swift_Message($subject))
        ->setFrom($from)
        ->setTo($to)
        ->setBody(
            $this->renderView(
                // templates/emails/type.html.twig
                $view,
                $data
            ),
            'text/html'
        );

        try {
            $success = $this->mailer->send($message);

            if (!$success) {
                throw new Exception('SwiftMailer unexpected error.');
            }
        } catch (\Swift_TransportException $e) {
            throw new Exception($e->getMessage());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getEmailUrl($link, $url)
    {
        return $_ENV['CLIENT_BASEURL'].$url.$link;
    }
}
