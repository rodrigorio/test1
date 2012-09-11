<?php

/**
 * @author Matias Velilla
 */
class MailerHelper
{
    /**
     *
     * @param <type> $from_address
     * @param <type> $from_name
     * @param <type> $destination_address
     * @param <type> $destination_name
     * @param <type> $subject
     * @param <type> $message_body
     * @param <type> $Cc
     * @return boolean 
     */
    function sendMail($from_address, $from_name, $destination_address, $destination_name, $subject, $message_body, $Cc = ""){

        $from_name = utf8_encode(html_entity_decode($from_name));
        $from_name = "=?UTF-8?B?".base64_encode($from_name)."?=\n";

        $subject = utf8_encode(html_entity_decode($subject));
        $subject = "=?UTF-8?B?".base64_encode($subject)."?=\n";

        $destination_name = utf8_encode(html_entity_decode($destination_name));
        $destination_name = "=?UTF-8?B?".base64_encode($destination_name)."?=\n";

        $message_body = utf8_encode($message_body);

        $headers  = "From: $from_name <$from_address> \n";
            if ($Cc != ""){$headers .= $Cc;}
            $headers .= "Content-type: text/html; charset=utf-8 \n";
            $headers .= "Reply-To: $from_name <$from_address> \n";
            $headers .= "Return-Path: $from_name <$from_address> \n";
            $headers .= "MIME-Version: 1.0 \n";

        return mail($destination_address, $subject, $message_body, $headers);
    }
}