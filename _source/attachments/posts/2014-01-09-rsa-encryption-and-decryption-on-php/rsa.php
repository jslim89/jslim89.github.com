<?php
// http://3stepsbeyond.co.uk/2010/12/openssl-and-php-tutorial-part-two/
$input = getopt('t:', array('text:'));
if (empty($input)) die('Please pass in the input. e.g. php rsa.php --text="You text here"'.PHP_EOL);
$plain_text = isset($input['t']) ? $input['t'] : $input['text'];

// encryption
$public_key = openssl_pkey_get_public(file_get_contents('public_key.pem'));
$public_key_details = openssl_pkey_get_details($public_key);
$encrypt_chunk_size = ceil($public_key_details['bits'] / 8) - 11;
$output = '';
while ($plain_text) {
    $chunk = substr($plain_text, 0, $encrypt_chunk_size);
    $plain_text = substr($plain_text, $encrypt_chunk_size);
    $encrypted = '';
    if (!openssl_public_encrypt($chunk, $encrypted, $public_key))
        die('Failed to encrypt data');
    $output .= $encrypted;
}
openssl_free_key($public_key);
$cipher_text = base64_encode($output);
echo $cipher_text;
echo PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;

// decryption
$encrypted = base64_decode($cipher_text);
$private_key = openssl_pkey_get_private(file_get_contents('private_key.pem'));
$private_key_details = openssl_pkey_get_details($private_key);
$decrypt_chunk_size = ceil($private_key_details['bits'] / 8);
$output = '';
while ($encrypted) {
    $chunk = substr($encrypted, 0, $decrypt_chunk_size);
    $encrypted = substr($encrypted, $decrypt_chunk_size);
    $decrypted = '';
    if (!openssl_private_decrypt($chunk, $decrypted, $private_key))
        die('Failed to decrypt data');
    $output .= $decrypted;
}
openssl_free_key($private_key);
echo $output.PHP_EOL;
