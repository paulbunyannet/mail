<?php
/**
 * Get Mail test
 *
 * Created 9/24/15 8:27 AM
 * Tests for GetMail class
 *
 * @author Nate Nolting <naten@paulbunyan.net>
 * @package Pbc\Mail
 */

namespace Pbc\Mail;


use Faker\Factory;

/**
 * Class GetMailTest
 * @package Pbc\Mail
 */
class GetMailTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $from = 'from@email.com';

    /**
     * @var string
     */
    protected $fromName = '';
    /**
     * @var string
     */
    protected $to = 'to@email.com';

    /**
     * @var string
     */
    protected $toName = '';

    /**
     * @var string
     */
    protected $subject = '';
    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var string
     */
    protected $server = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 1025;

    /**
     * @var
     */
    protected $send;


    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->faker = Factory::create();
    }

    /**
     *
     */
    protected function init()
    {
        $this->setSubject(implode(' ', $this->faker->words(3)));
        $this->setBody($this->faker->paragraph);
        $this->setTo(rand(1, 999) . $this->faker->email);
        $this->setToName($this->faker->name);
        $this->setFrom(rand(9999, 99999) . $this->faker->email);
        $this->setFromName($this->faker->name);
        $this->setServer('127.0.0.1');
        $this->setPort('1025');

        $transport = \Swift_SmtpTransport::newInstance(
            "localhost", 1025
        );
        $mailer = \Swift_Mailer::newInstance($transport);

        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject($this->getSubject())
            ->setFrom($this->getFrom(), $this->getFromName())
            ->setTo($this->getTo(), $this->getToName())
            ->setBody($this->getBody());

        $this->setSend($mailer->send($message));


    }

    /**
     * @todo find mocking workaround for this test, rather than using a live account
     */
    public function testGetMail()
    {
        $this->init();
        $this->assertEquals(1, intval($this->getSend()));

        #$get = new GetMail(['username' => 'root', 'password' => 'root', 'email' => $this->getTo(), 'mailServer' => $this->getServer(), 'port' => $this->getPort(), 'validateCert' => false]);

    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getToName()
    {
        return $this->toName;
    }

    /**
     * @param string $toName
     */
    public function setToName($toName)
    {
        $this->toName = $toName;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param string $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getSend()
    {
        return $this->send;
    }

    /**
     * @param mixed $send
     */
    public function setSend($send)
    {
        $this->send = $send;
    }

}
