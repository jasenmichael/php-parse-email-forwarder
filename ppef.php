#!/usr/bin/php
<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__ . '/vendor/autoload.php';

if (php_sapi_name() == "cli") {
    // In cli-mode
    // print_r('yay cli');
} else {
    // Not in cli-mode
    print_r('not cli');
    exit;
};

$parser = new PhpMimeMailParser\Parser();

// $path = 'email.eml';
$path = "php://stdin";

$parser->setPath($path);

// get the body text
$text = $parser->getMessageBody('text');
// remove everything after "* Used" - (removes all the stuff I don't need)
$body = explode('* Used ', $text)[0];

// get the attachments
$attachments = $parser->getAttachments();

echo "\r\n\r";
print_r("phone: " . getPhone($body));
echo "\r\n\r";
print_r("msg len: " . getMsgLength($body));
echo "\r\n\r";
print_r("attachment: " . saveAttachment($attachments));
echo "\r\n\r";

// build theresponse data
$data = array(
    "phone" => getPhone($body),
    "msgLen" => getMsgLength($body)
);

// functions
function getPhone($body)
{
    $phone = explode("From: ", explode('Length: ', $body)[0])[1];
    return str_replace(") ", ")", trim($phone));
}

function getMsgLength($body)
{
    $msgLength = explode("To:   ", explode("Length: ", $body)[1])[0];
    return trim($msgLength);
}

function getWhenMsgSent()
{
    $whenMsgSent = "whenMsgSent test";
    return $whenMsgSent;
}

function saveAttachment($attachments)
{
    foreach ($attachments as $attachment) {
        $fileName = $attachment->getFilename();
        $tmpPath = 'messages/';
        if (file_exists($tmpPath . $fileName)) {
            // echo "The file $fileName exists";
            unlink($tmpPath . $fileName);
            // echo "\r\n\r";
        }
        $attachment->save($tmpPath);
        return dirname(__FILE__) . "/" . $tmpPath . $fileName;
    }
    // $attachment = "attachment test";
    // return $attachments;
}
