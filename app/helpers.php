<?php

function decryptCryptoJsAes(string $jsonStr, string $passphrase)
{
    $json = json_decode($jsonStr, true);
    $salt = hex2bin($json["s"]);
    $iv = hex2bin($json["iv"]);
    $ct = base64_decode($json["ct"]);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = [];
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}