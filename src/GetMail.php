<?php
/*
 * File: GetMail.php
 * Description: Receiving mail With Attachments
 *
 * @package Pbc\Mail
 * @author Mitul Koradia <mitulkoradia@gmail.com>
 * @author Nate Nolting <naten@paulbunyan.net>
 * @version: 1.1
 */

namespace Pbc\Mail;

/**
 * Class GetMail
 * @package Pbc\Mail
 */
class GetMail
{
    /**
     * @var string
     */
    private $server = '';
    /**
     * @var string
     */
    private $username = '';
    /**
     * @var string
     */
    private $password = '';
    /**
     * @var resource
     */
    private $mailBox = '';
    /**
     * @var string
     */
    private $email = '';

    /**
     * @var string
     */
    private $mailServer = 'localhost';

    /**
     * @var string
     */
    private $serverType = 'pop';

    /**
     * @var string
     */
    private $port = '110';

    /**
     * @var bool
     */
    private $ssl = false;

    /**
     * @var bool
     */
    private $validateCert = false;

    /**
     * @param array $properties
     * @throws \Exception
     */
    public function __construct(array $properties = [
        'username' => 'username',
        'password' => 'password',
        'email' => 'someone@somewhere.com',
        'mailServer' => '127.0.0.1',
        'serverType' => 'pop',
        'port' => 1025,
        'ssl' => false,
        'validateCert' => false
    ])
    {
        foreach ($properties as $key => $val) {
            if (method_exists($this, 'set' . ucfirst($key))) {
                $this->{'set' . ucfirst($key)}($val);
            }
        }

        if ($this->getServerType() === 'imap') {
            if ($this->getPort() == '') {
                $this->setPort('143');
            }
            $strConnect = '{' . $this->getMailServer() . ':' . $this->getPort() . '/imap'. ($this->isSsl() ? "/ssl" : null) . (!$this->isValidateCert() ? '/novalidate-cert' : null) . '}INBOX';
        } else {
            $strConnect = '{' . $this->getMailServer() . ':' . $this->getPort() . '/pop3' . ($this->isSsl() ? "/ssl" : null) . (!$this->isValidateCert() ? '/novalidate-cert' : null) . '}INBOX';
        }
        $this->setServer($strConnect);

        try {
            return $this->connect();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage() . ' ' . print_r($this, true));
        }

    }

    /**
     * @return string
     */
    public function getServerType()
    {
        return $this->serverType;
    }

    /**
     * @param string $serverType
     */
    protected function setServerType($serverType)
    {
        $this->serverType = $serverType;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    protected function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getMailServer()
    {
        return $this->mailServer;
    }

    /**
     * @param string $mailServer
     */
    protected function setMailServer($mailServer)
    {
        $this->mailServer = $mailServer;
    }

    /**
     * @return boolean
     */
    public function isSsl()
    {
        return (bool)$this->ssl;
    }

    /**
     * @param boolean $ssl
     */
    protected function setSsl($ssl)
    {
        $this->ssl = $ssl;
    }

    /**
     * @return boolean
     */
    public function isValidateCert()
    {
        return (bool)$this->validateCert;
    }

    /**
     * @param boolean $validateCert
     */
    protected function setValidateCert($validateCert)
    {
        $this->validateCert = $validateCert;
    }

    /**
     * @param string $server
     */
    private function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * Connect To the Mail Box
     */
    public function connect()
    {
        if (!function_exists('imap_open')) {
            throw new \Exception('imap_open function must be enabled');
        }
        $this->setMailBox(@imap_open($this->getServer(), $this->getUsername(), $this->getPassword(), OP_SILENT));
        $errors = imap_errors();
        $alerts = imap_alerts();
        if (!$this->getMailBox()) {
            if (!$errors) {
                $errors = array();
            }
            if (!$alerts) {
                $alerts = array();
            }

            throw new \Exception("Error: " . implode('. ', array_merge($errors, $alerts)));
        }
        return $this->getMailBox();
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    protected function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    protected function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return resource
     */
    public function getMailBox()
    {
        return $this->mailBox;
    }

    /**
     * @param resource $mailBox
     */
    protected function setMailBox($mailBox)
    {
        $this->mailBox = $mailBox;
    }

    /**
     * Convert a utf8 subject line into a readable one
     * http://stackoverflow.com/a/22808461/405758
     * http://stackoverflow.com/a/9411054/405758
     *
     * @param $str
     * @return string
     */
    public static function imapUtf8($str)
    {
        $convStr = '';
        $subLines = preg_split('/[\r\n]+/', $str);
        for ($i = 0; $i < count($subLines); $i++) {
            $convLine = '';
            $linePartArr = imap_mime_header_decode($subLines[$i]);
            for ($j = 0; $j < count($linePartArr); $j++) {
                if ($linePartArr[$j]->charset === 'default') {
                    if ($linePartArr[$j]->text != " ") {
                        $convLine .= ($linePartArr[$j]->text);
                    }
                } else {
                    $convLine .= iconv($linePartArr[$j]->charset, 'UTF-8', $linePartArr[$j]->text);
                }
            }
            $convStr .= $convLine;
        }

        return $convStr;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    protected function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get all headers
     *
     * @return array
     */
    public function getAllHeaders()
    {
        return imap_headers($this->getMailBox());
    }

    /**
     * Get Header info
     * @param $mid
     *
     * @return array
     */
    public function getHeaders($mid)
    {
        if (!$this->getMailBox()) {
            return false;
        }

        $mailHeader = imap_header($this->getMailBox(), $mid);
        $sender = $mailHeader->from[0];
        $senderReplyTo = $mailHeader->reply_to[0];

        $mailDetails = array();
        if (strtolower($sender->mailbox) != 'mailer-daemon' && strtolower($sender->mailbox) != 'postmaster') {
            $mailDetails = array(
                'from' => strtolower($sender->mailbox) . '@' . $sender->host,
                'fromName' => property_exists($sender, 'personal') ? $sender->personal : null,
                'toOth' => strtolower($senderReplyTo->mailbox) . '@' . $senderReplyTo->host,
                'toNameOth' => property_exists($senderReplyTo, 'personal') ? $senderReplyTo->personal : null,
                'subject' => $mailHeader->subject,
                'to' => strtolower($mailHeader->toaddress)
            );
        }
        return $mailDetails;
    }

    /**
     * Get Total Number off Unread Email In Mailbox
     * @return bool|int
     */
    public function getTotalMails()
    {
        if (!$this->getMailBox()) {
            return false;
        }

        $headers = imap_headers($this->getMailBox());
        return count($headers);
    }

    /**
     * Get Attached File from Mail
     *
     * @param int $mid message id
     * @param string $path attachment storage path
     *
     * @param string $separator list separator for attachment names
     *
     * @return string
     */
    public function getAttachment($mid, $path, $separator = ',')
    {
        if (!$this->getMailBox()) {
            return false;
        }

        $structure = imap_fetchstructure($this->getMailBox(), $mid);
        $attachments = "";
        if (property_exists($structure, 'parts') && $structure->parts) {
            foreach (array_keys($structure->parts) as $key) {
                $enc = $structure->parts[$key]->encoding;
                if ($structure->parts[$key]->ifdparameters) {
                    $name = $structure->parts[$key]->dparameters[0]->value;
                    $message = imap_fetchbody($this->getMailBox(), $mid, $key + 1);
                    switch ($enc) {
                        case(0):
                            $message = imap_8bit($message);
                            break;
                        case(1):
                            $message = imap_8bit($message);
                            break;
                        case(2):
                            $message = imap_binary($message);
                            break;
                        case(3):
                            $message = imap_base64($message);
                            break;
                        case(4):
                            $message = quoted_printable_decode($message);
                            break;
                    }
                    $fp = fopen($path . $name, "w");
                    fwrite($fp, $message);
                    fclose($fp);
                    $attachments .= $name . $separator;
                }
                // Support for embedded attachments starts here
                if (property_exists($structure->parts[$key], 'parts')) {
                    foreach (array_keys($structure->parts[$key]->parts) as $keyB) {
                        $enc = $structure->parts[$key]->parts[$keyB]->encoding;
                        if ($structure->parts[$key]->parts[$keyB]->ifdparameters) {
                            $name = $structure->parts[$key]->parts[$keyB]->dparameters[0]->value;
                            $partNum = ($key + 1) . "." . ($keyB + 1);
                            $message = imap_fetchbody($this->getMailBox(), $mid, $partNum);
                            switch ($enc) {
                                case(0):
                                    $message = imap_8bit($message);
                                    break;
                                case(1):
                                    $message = imap_8bit($message);
                                    break;
                                case(2):
                                    $message = imap_binary($message);
                                    break;
                                case(3):
                                    $message = imap_base64($message);
                                    break;
                                case(4):
                                    $message = quoted_printable_decode($message);
                                    break;
                            }

                            $fp = fopen($path . $name, "w");
                            fwrite($fp, $message);
                            fclose($fp);
                            $attachments .= $name . $separator;
                        }
                    }
                }
            }
        }

        /** Catch embedded images  */
        $embedded = $this->getEmbeddedImages(
            array(
                'mid' => $mid,
                'path' => $path,
                'separator' => $separator
            )
        );
        if ($embedded) {
            $attachments = $attachments . $embedded;
        }

        $attachments = substr($attachments, 0, (strlen($attachments) - strlen($separator)));
        return $attachments;
    }

    /**
     * Get inlined base 64 images from message body
     *
     * @param $attributes
     *
     * @return null
     */
    private function getEmbeddedImages($attributes) // get inline base64 images in message part
    {
        /** @var object $part message part */
        /** @var int $mid message ID */
        /** @var int $key key from full message parts */
        /** @var string $path folder path to write attachment to */
        /** @var string $attachments list of attachments */
        /** @var string $separator separator string */

        $attachments = null;
        $path = null;
        $mid = null;
        $separator = null;
        $key = null;

        extract($this->defaultAttributes(
            array(
                'mid' => null,
                'path' => null,
                'separator' => ',',
                'sub' => false,
                'key' => 1,
                'part' => false
            ),
            $attributes
        ), EXTR_OVERWRITE);

        /**
         * If no part exists get the structure of the message id,
         * this will start up getting parts of the message
         */
        if (!$part) {
            $structure = imap_fetchstructure($this->getMailBox(), $mid);
            /**
             * if there's the parts property loop over it and see if there are more child parts,
             * if not get the embedded image from this part.
             */
            if (property_exists($structure, 'parts') && count($structure->parts) > 0) {
                foreach ($structure->parts as $key => $value) {
                    $attachments .= $this->getEmbeddedImages(
                        array(
                            'mid' => $mid,
                            'path' => $path,
                            'separator' => $separator,
                            'key' => $key + 1,
                            'part' => $value
                        )
                    );
                }
            } else {
                $attachments .= $this->getEmbeddedImage(array(
                    'mid' => $mid,
                    'path' => $path,
                    'separator' => $separator,
                    'part' => $structure,
                    'key' => $key + 1
                ));
            }
        } else {
            /**
             * If part is set and there are child parts to this part then loop over and
             * get embedded image, otherwise get the embed image from this part
             */
            if (is_object($part) && property_exists($part, 'parts')) {
                foreach ($part->parts as $keyb => $valueb) {
                    if (property_exists($valueb, 'parts') && count($valueb->parts) > 0) {
                        $attachments .= $this->getEmbeddedImages(array(
                            'mid' => $mid,
                            'path' => $path,
                            'separator' => $separator,
                            'part' => $valueb->parts,
                            'key' => $key . '.' . ($keyb + 1)
                        ));
                    } else {

                        $attachments .= $this->getEmbeddedImage(array(
                            'mid' => $mid,
                            'path' => $path,
                            'separator' => $separator,
                            'part' => $valueb,
                            'key' => $key . '.' . ($keyb + 1)
                        ));
                    }
                }
                /**
                 * if this part and array? Loop over and get the embedded images
                 */
            } elseif (is_array($part) && isset($part[0])) {
                foreach ($part as $keyc => $valuec) {
                    $attachments .= $this->getEmbeddedImages(array(
                        'mid' => $mid,
                        'path' => $path,
                        'separator' => $separator,
                        'part' => $valuec,
                        'key' => $key . '.' . ($keyc + 1)
                    ));
                }
            } else {
                /** this is the drilled down version, use the given key and try and retrieve the inline image. */
                $attachments .= $this->getEmbeddedImage(array(
                    'mid' => $mid,
                    'path' => $path,
                    'separator' => $separator,
                    'part' => $part,
                    'key' => $key
                ));
            }
        }


        return $attachments;
    }

    /**
     * Set default Attributes for method
     *
     * @param     $pairs
     * @param     $atts
     *
     * @return array|bool
     */
    protected function defaultAttributes($pairs, $atts)
    {

        foreach ($atts as $name => $value) {
            if (array_key_exists($name, $pairs)) {
                $pairs[$name] = $value;
            }
        }

        return ($pairs) ? $pairs : false;
    }

    /**
     * get inline image from part
     *
     * @param $attributes
     *
     * @return null|string
     */
    private function getEmbeddedImage($attributes)
    {
        $part = new \stdClass();
        $mid = null;
        $key = null;
        $path = null;
        $attachments = null;
        $separator = null;
        extract($this->defaultAttributes(
            array(
                'mid' => null,
                'path' => null,
                'separator' => ',',
                'sub' => false,
                'key' => 1,
                'part' => new \stdClass()
            ),
            $attributes
        ), EXTR_OVERWRITE);


        if ($part->type === 5 && $part->bytes > 0 && $part->encoding === 3) {

            $message = imap_base64(imap_fetchbody($this->getMailBox(), $mid, $key));

            if ($message) {
                $fileName = md5($message) . '.' . $part->subtype;
                foreach ($part->parameters as $param) {
                    if ($param->attribute === 'NAME') {
                        $fileName = $param->value;
                        break;
                    }
                }
                $filePut = fopen($path . $fileName, "w");
                fwrite($filePut, $message);
                fclose($filePut);
                $attachments .= $fileName . $separator;
            }

        }

        return $attachments;

    }

    /**
     * Get Message Body
     *
     * @param $mid
     *
     * @return bool|string
     */
    public function getBody($mid)
    {
        if (!$this->getMailBox()) {
            return false;
        }

        $body = $this->getPart($this->getMailBox(), $mid, "TEXT/HTML");
        if ($body == "") {
            $body = $this->getPart($this->getMailBox(), $mid, "TEXT/PLAIN");
        }
        if ($body == "") {
            return "";
        }
        return $body;
    }

    /**
     * Get part of message
     *
     * @param      $stream
     * @param      $msgNumber
     * @param      $mimeType
     * @param bool $structure
     * @param bool $partNumber
     *
     * @return mixed
     */
    public function getPart(
        $stream,
        $msgNumber,
        $mimeType,
        $structure = false,
        $partNumber = false
    ) //Get Part Of Message Internal Private Use
    {
        if (!$structure) {
            $structure = imap_fetchstructure($stream, $msgNumber);
        }
        if ($structure) {
            if ($mimeType == $this->getMimeType($structure)) {
                if (!$partNumber) {
                    $partNumber = "1";
                }
                $text = imap_fetchbody($stream, $msgNumber, $partNumber);
                if ($structure->encoding == 3) {
                    return imap_base64($text);
                } else {
                    if ($structure->encoding == 4) {
                        return imap_qprint($text);
                    } else {
                        return $text;
                    }
                }
            }
            $prefix = null;
            if ($structure->type == 1) /* multipart */ {
                while (list($index, $subStructure) = each($structure->parts)) {
                    if ($partNumber) {
                        $prefix = $partNumber . '.';
                    }
                    $data = $this->getPart($stream, $msgNumber, $mimeType, $subStructure, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get Mime type
     * @param $structure
     *
     * @return string
     */
    private function getMimeType(&$structure)
    {
        $primaryMimeType = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

        if ($structure->subtype) {
            return $primaryMimeType[(int)$structure->type] . '/' . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }

    /**
     * Get full body of message
     *
     * @param $mid
     *
     * @return bool|string
     */
    public function getFullBody($mid)
    {
        if (!$this->getMailBox()) {
            return false;
        }

        return imap_body($this->getMailBox(), $mid);

    }

    /**
     * Mark a message for deletion
     *
     * @param $mid
     *
     * @return bool
     */
    public function deleteMails($mid)
    {
        if (!$this->getMailBox()) {
            return false;
        }

        return imap_delete($this->getMailBox(), $mid);
    }

    /**
     *
     * Close Mail Box
     * @return bool
     */
    public function closeMailbox()
    {
        if (!$this->getMailBox()) {
            return false;
        }

        return imap_close($this->getMailBox(), CL_EXPUNGE);
    }

}