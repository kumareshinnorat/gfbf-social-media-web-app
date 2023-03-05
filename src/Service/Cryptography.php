<?php

namespace App\Service;

/**
 *  This class encode and decode the string values and return it back with
 *  encrypted or decrypted string.
 * 
 *  @method encode()
 *    encode is used for encrypting message.
 *  @method decode()
 *    decode is used for decrypting message. 
 *  
 *  @author Kumaresh Baksi <kumaresh.baksi@innoraft.com>
 */
class Cryptography
{    
    /**
     *  encode function uses base64 encoding function to encrypt the string
     *
     *  @param  mixed $msg
     *    This is the string which will be encrypted.
     * 
     *  @return string
     *    Method returns a encoded message.
     */
    public function encode(string $msg)
    {
        return urlencode(base64_encode($msg));
    }

    /**
     *  decode function uses base64 decoding function to decrypt the string
     *
     *  @param  mixed $msg
     *    $msg is the string which will be decrypted.
     * 
     *  @return string
     *    Method returns a decoded message.
     */
    public function decode(string $msg)
    {
      return base64_decode(urldecode($msg));
    }
}