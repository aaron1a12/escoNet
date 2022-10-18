<?php
define( 'SYSTEM_ENCRYPT_KEY', "DS54F#%*s$33J7#\0" );

function encrypt($data)
{
    /*$ivlen = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivlen);
    print($iv);
    return urlencode(
        base64_encode(
            openssl_encrypt($data, 'aes-256-cbc', SYSTEM_ENCRYPT_KEY, OPENSSL_RAW_DATA, $iv)
        )
    );*/

    
    return urlencode(
        base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                SYSTEM_ENCRYPT_KEY,
                $data,
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
            )
        )
    );
}

function decrypt($data)
{
    //$ivlen = openssl_cipher_iv_length('aes-256-cbc');
    //$iv = openssl_random_pseudo_bytes($ivlen);

    //return openssl_decrypt($data, 'aes-256-cbc', SYSTEM_ENCRYPT_KEY, OPENSSL_RAW_DATA, $iv);

    return mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                SYSTEM_ENCRYPT_KEY,
                base64_decode(urldecode($data)),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
    );
}

function decrypt_url($data)
{
    return preg_replace('/[^\da-z-_\/]/i', '', decrypt(base64_decode($data)) );

    /*return preg_replace('/[^\da-z-_\/]/i', '', mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                SYSTEM_ENCRYPT_KEY,
                base64_decode($data),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
    ) );*/
}