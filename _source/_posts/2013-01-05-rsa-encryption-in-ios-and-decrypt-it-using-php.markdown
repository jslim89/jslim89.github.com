---
layout: post
title: "RSA encryption in iOS and decrypt it using PHP"
date: 2013-01-05 13:20
comments: true
categories: 
- ios
- php
---

I've been suffer for few weeks on RSA encryption in iOS. Now I would like to share the way of doing this.

First, generate a key-pair using SSL.

```
$ openssl req -x509 -out public_key.der -outform der -new -newkey rsa:1024 -keyout private_key.pem -days 3650
```

There are few points have to mention:

- `public_key.der` is an output based on x509 certificate. Note that in iOS must be `.der` format but not `.pem`
- `private_key.pem` is the private key that you can use it to decrypt
- `rsa:1024` is the key length. The longer the length, the safer it is
- `-days` is the days for effective period for this cert. In this case, is 10-Years

Now, drag `public_key.der` to your iOS project and create 2 files: **RSA.h** and **RSA.m**

**RSA.h**

```obj-c
#import <Foundation/Foundation.h>

@interface RSA : NSObject {
    SecKeyRef publicKey;
    SecCertificateRef certificate;
    SecPolicyRef policy;
    SecTrustRef trust;
    size_t maxPlainLen;
}
- (NSData *) encryptWithData:(NSData *)content;
- (NSData *) encryptWithString:(NSString *)content;
- (NSString *) encryptToString:(NSString *)content;

@end
```

**RSA.m**

```obj-c
#import "RSA.h"

@implementation RSA
 
- (id)init {
    self = [super init];
     
    NSString *publicKeyPath = [[NSBundle mainBundle] pathForResource:@"public_key"
                                                     ofType:@"der"];
    if (publicKeyPath == nil) {
        NSLog(@"Can not find pub.der");
        return nil;
    }
     
    NSDate *publicKeyFileContent = [NSData dataWithContentsOfFile:publicKeyPath];
    if (publicKeyFileContent == nil) {
        NSLog(@"Can not read from pub.der");
        return nil;
    }
     
    certificate = SecCertificateCreateWithData(kCFAllocatorDefault, ( __bridge CFDataRef)publicKeyFileContent);
    if (certificate == nil) {
        NSLog(@"Can not read certificate from pub.der");
        return nil;
    }
     
    policy = SecPolicyCreateBasicX509();
    OSStatus returnCode = SecTrustCreateWithCertificates(certificate, policy, &trust);
    if (returnCode != 0) {
        NSLog(@"SecTrustCreateWithCertificates fail. Error Code: %ld", returnCode);
        return nil;
    }
     
    SecTrustResultType trustResultType;
    returnCode = SecTrustEvaluate(trust, &trustResultType);
    if (returnCode != 0) {
        return nil;
    }
     
    publicKey = SecTrustCopyPublicKey(trust);
    if (publicKey == nil) {
        NSLog(@"SecTrustCopyPublicKey fail");
        return nil;
    }
     
    maxPlainLen = SecKeyGetBlockSize(publicKey) - 12;
    return self;
}
 
- (NSData *) encryptWithData:(NSData *)content {
     
    size_t plainLen = [content length];
    if (plainLen > maxPlainLen) {
        NSLog(@"content(%ld) is too long, must < %ld", plainLen, maxPlainLen);
        return nil;
    }
     
    void *plain = malloc(plainLen);
    [content getBytes:plain
               length:plainLen];
     
    size_t cipherLen = 128; // currently RSA key length is set to 128 bytes
    void *cipher = malloc(cipherLen);
     
    OSStatus returnCode = SecKeyEncrypt(publicKey, kSecPaddingPKCS1, plain,
                                        plainLen, cipher, &cipherLen);
     
    NSData *result = nil;
    if (returnCode != 0) {
        NSLog(@"SecKeyEncrypt fail. Error Code: %ld", returnCode);
    }
    else {
        result = [NSData dataWithBytes:cipher
                                length:cipherLen];
    }
     
    free(plain);
    free(cipher);
     
    return result;
}
 
- (NSData *) encryptWithString:(NSString *)content {
    return [self encryptWithData:[content dataUsingEncoding:NSUTF8StringEncoding]];
}

- (NSString *) encryptToString:(NSString *)content {
    NSData *data = [self encryptWithString:content];
    return [self base64forData:data];
}

// convert NSData to NSString
- (NSString*)base64forData:(NSData*)theData {
    const uint8_t* input = (const uint8_t*)[theData bytes];
    NSInteger length = [theData length];

    static char table[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

    NSMutableData* data = [NSMutableData dataWithLength:((length + 2) / 3) * 4];
    uint8_t* output = (uint8_t*)data.mutableBytes;

    NSInteger i;
    for (i=0; i < length; i += 3) {
        NSInteger value = 0;
        NSInteger j;
        for (j = i; j < (i + 3); j++) {
            value <<= 8;

            if (j < length) {
                value |= (0xFF & input[j]);
            }
        }

        NSInteger theIndex = (i / 3) * 4;
        output[theIndex + 0] =                    table[(value >> 18) & 0x3F];
        output[theIndex + 1] =                    table[(value >> 12) & 0x3F];
        output[theIndex + 2] = (i + 1) < length ? table[(value >> 6)  & 0x3F] : '=';
        output[theIndex + 3] = (i + 2) < length ? table[(value >> 0)  & 0x3F] : '=';
    }

    return [[[NSString alloc] initWithData:data encoding:NSASCIIStringEncoding] autorelease];
}
 
- (void)dealloc{
    CFRelease(certificate);
    CFRelease(trust);
    CFRelease(policy);
    CFRelease(publicKey);
}
 
@end
```

**Usage**

```obj-c
#import "RSA.h"

RSA *rsa = [[RSA alloc] init];
if (rsa != nil) {
    // just post the string to server
    NSLog(@"%@", [rsa encryptToString:@"This is plaintext"]);
} else {
    NSLog(@"Error");
}
```

The iOS part is done. Now let's decrypt in PHP. Before that, let's download [phpseclib](http://phpseclib.sourceforge.net/) for decryption.

```php
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Crypt/RSA.php');

$rsa = new Crypt_RSA();
$rsa->setPassword('yourPassword');
$rsa->loadKey(file_get_contents('/path/to/private_key.pem'));

$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

echo $rsa->decrypt(base64_decode($_POST['ciphertext']));
```

Have fun :)

_References:_

- _[RSA iOS encrypt & PHP decrypt](http://stackoverflow.com/questions/14018651/rsa-ios-encrypt-php-decrypt)_
- _[iOS下的RSA加密方法](http://blog.iamzsx.me/show.html?id=155002)_
