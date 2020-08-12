---
layout: post
title: "RSA encryption &amp; decryption on PHP"
date: 2014-01-09 06:59
comments: true
tags: 
- php
---

I've come across RSA encryption on a very long string.

The solution is divide the long string into chunks according to the length of the key.

## Generate key pair

Example here shows 512 bits of encryption

Generate private key

```
$ openssl genrsa -out private_key.pem 512
```

Generate public key from private key

```
$ openssl rsa -in private_key.pem -pubout -out public_key.pem
```

## Encryption

```php
<?php
$plain_text = 'Text you want to encrypt. e.g. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

// read the public key
$public_key = openssl_pkey_get_public(file_get_contents('public_key.pem'));
$public_key_details = openssl_pkey_get_details($public_key);
// there are 11 bytes overhead for PKCS1 padding
$encrypt_chunk_size = ceil($public_key_details['bits'] / 8) - 11;
$output = '';
// loop through the long plain text, and divide by chunks
while ($plain_text) {
    $chunk = substr($plain_text, 0, $encrypt_chunk_size);
    $plain_text = substr($plain_text, $encrypt_chunk_size);
    $encrypted = '';
    if (!openssl_public_encrypt($chunk, $encrypted, $public_key))
        die('Failed to encrypt data');
    $output .= $encrypted;
}
openssl_free_key($public_key);
echo base64_encode($output);
```

## Decryption

```php
<?php
$cipher_text = 'RZMrhPib29rq1ghwS+F2/oooG0tYW1p8MVKMkyNHQWkH/GyCJY74dbcO4DB/brB9W3UzNW63NV0N9Vqw3z+eCQi13eZBAKMBJ+2Y2YVh1UhGcG22orb5v0rRmLN3DpUz0wDyr5eFGXoyT+x9RgctMr2fVhFnMu5pXMLPgkC1LU4tBcU+LBdbn+1wq9CbxjtcRckmOeFbMwrX/vUrVbcwlxEIddgrEfr9xyONwE0XW4DEyHxNvv2bsP9c2SSwJm4nA/3HiEWqzPQzV1ygUWw+xd/GE7+QfCWeet8BQXAYbCVfmDaElHMb08M98g6hZC9w0GE2Qo7sw/JfOJm/xuR1Ths2mQPVwhGj+Z4feWxMi9o5LcZupyfpITuVd2C8uzjBWRjcqIZP1iXuj5etFKVgKFnnvN5fVxax3vBgwJ/AeZQdlvxy1BCEdvevSnDugEiThlXFB9uHA6126cD6F6OTDmknajb2U9BqkFqWfD+s44VIaQcrq8BLs7ZYWW9gtw1qNLBB7bVZL7w2u5pbSY7LugRERCJvS4bVh6xawnzUi5AHs+9x2LUMJWIe5zwdjGO2qBdgXpTDko+vFUVhzr15XBnKNb1TyXzIaHMwsgoXK8jnFuJu0I8ql+TlbLs0JlxML9Nlu//K11kaSV8mEEN/fp5lUmpVbB4MYRlYLFwkHvk3SJYoShK7im5HTmm4qZ8W40+PkFlMz7H/Jikm3wMuQh/QSwpBkqMo9xBG9L9spIz1M3r0auzV1Wrqvz0Q8b28';

// decode the text to bytes
$encrypted = base64_decode($cipher_text);

// read the private key
$private_key = openssl_pkey_get_private(file_get_contents('private_key.pem'));
$private_key_details = openssl_pkey_get_details($private_key);

// there is no need to minus the overhead
$decrypt_chunk_size = ceil($private_key_details['bits'] / 8);
$output = '';

// decrypt it back chunk-by-chunk
while ($encrypted) {
    $chunk = substr($encrypted, 0, $decrypt_chunk_size);
    $encrypted = substr($encrypted, $decrypt_chunk_size);
    $decrypted = '';
    if (!openssl_private_decrypt($chunk, $decrypted, $private_key))
        die('Failed to decrypt data');
    $output .= $decrypted;
}
openssl_free_key($private_key);
echo $output;
```

You can download the [example here](/attachments/posts/2014-01-09-rsa-encryption-and-decryption-on-php/rsa.php).

_References:_

* _[openssl use RSA private key to generate public key?](http://stackoverflow.com/questions/5244129/openssl-use-rsa-private-key-to-generate-public-key/5246045#5246045)_
* _[OpenSSL and PHP Tutorial – Part Two](http://3stepsbeyond.co.uk/2010/12/openssl-and-php-tutorial-part-two/)_
