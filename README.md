# paulbunyannet/mail 

[![Build Status](https://travis-ci.org/paulbunyannet/mail.svg?branch=master)](https://travis-ci.org/paulbunyannet/mail)
[![Latest Version](https://img.shields.io/packagist/v/paulbunyannet/mail.svg?style=flat-square)](https://packagist.org/packages/paulbunyannet/mail)

**paulbunyannet/mail** Shortcut for checking a mail box


## Installation

This project can be installed via [Composer]:

``` bash
$ composer require paulbunyannet/mail:"^1.0"
```

## Get Mail

```php

$mailbox = new Pbc\Mail\GetMail([
    'username' => emailUsername,
    'password' => emailPassword,
    'email' => someone@email.com,
    'mailServer' => mail.email.com,
]);

// get total messages in box
$totalMail = $mailbox->getTotalMails();
for ($i = 1; $i <= $totalMail; $i++) {
    $headers = $mailbox->getHeaders($i);
    echo "<h1>" . $headers['subject'] . '</h1>';
    echo "<p>From: " . $headers['from'] . '</p>';
    echo $mailbox->getFullBody($i);
}
```
