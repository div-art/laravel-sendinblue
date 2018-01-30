<?php

namespace DivArt\SendInBlue\Transport;

use GuzzleHttp\ClientInterface;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class SendInBlueTransport extends Transport
{
    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The SendInBlue API key.
     *
     * @var string
     */
    protected $key;

    /**
     * The SendInBlue domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * The SendInBlue API end-point.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new SendInBlue transport instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @param  string  $domain
     * @return void
     */
    public function __construct(ClientInterface $client, $key)
    {
        $this->key = $key;
        $this->client = $client;
        $this->url = 'https://api.sendinblue.com/v3/smtp/email';
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $to = $this->getTo($message);

        $from = $this->getFrom($message);

        $payload = [
            'headers' => [
                'content-type' => 'application/json',
                'api-key' => $this->key,
            ],
            'body' => json_encode([
                'sender' => [
                    'email' => $from,
                ],
                'to' => [
                    [
                        'email' => $to,
                        'name' => $to, 
                    ], 
                ],
                'htmlContent' => $message->getBody(),
                'subject' => $message->getSubject(),
                'replyTo' => [
                    'email' => $from,
                ],
            ]),
        ];

        $message->setBcc([]);

        $this->client->post($this->url, $payload);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return string
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($this->allContacts($message))->map(function ($display, $address) {
            return $display ? $display." <{$address}>" : $address;
        })->values()->implode(',');
    }

    protected function getFrom(Swift_Mime_SimpleMessage $message)
    {
        return collect($message->getFrom())->map(function ($display, $address) {
            return $address;
        })->values()->implode(',');
    }

    /**
     * Get all of the contacts for the message.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array) $message->getTo(), (array) $message->getCc(), (array) $message->getBcc()
        );
    }
    
    /**
     * Get the API key being used by the transport.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport.
     *
     * @param  string  $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }
}