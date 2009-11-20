<?php
// script to test mail
$recipients = 'dafyddmtjames@yahoo.co.uk';
$subject    = 'test subject';
$mailBody   = 'test body';
$headers    = '';
$mailStatus = mail($recipients, $subject, $mailBody, $headers);
