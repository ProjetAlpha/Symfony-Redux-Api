<?php

namespace App\Traits;

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

    public function processMail($from, $to, $subject, $data, $type = null)
    {
        $view = 'emails/'.($type ? $type : 'link').'.html.twig';
        $message = (new \Swift_Message($subject))
        ->setFrom($from)
        ->setTo($to)
        ->setBody(
            $this->renderView(
                // templates/emails/type.html.twig
                $view,
                $data
            )
        );

        // swift mailer use a custom stmp server
        $this->mailer->send($message);
    }
}
