---
layout: post
title: "RSA Decryption on iOS"
date: 2013-06-24 19:12
comments: true
categories: 
- ios
---

I've been googled for few days and struggling on **"How to decrypt cipher text in iOS"**, unfortunately there is no solution were found.

Then I decided to do it with C language rather than Objective-C, and I found a [library](https://github.com/st3fan/ios-openssl) for openssl that could be included to Xcode.

### Step 1: Install the library
Download the library, unzip it then drag the **lib** and **include** folders to your xcode project. Make sure the **Build Settings** -> **Header Search Paths** you have set properly (i.e. "${SRCROOT}/Libraries/openssl/include")

### Step 2: Add private key
Just drag your private key in **pem** format to xcode project.

**If your private key is in binary form, you may [refer here](http://www.herongyang.com/Cryptography/keytool-Export-Key-openssl-enc-Command.html).**

### Step 3: Create a custom class
Create a new class (subclass of NSObject), just named it **Crypto**

**Crypto.h**
```obj-c
#import <Foundation/Foundation.h>

@interface Crypto : NSObject

- (NSString *)decryptFromCipherText:(NSString *)cipherText;

@end
```

**Crypto.m**
```obj-c
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include <openssl/err.h>

#import "Crypto.h"

@interface Crypto ()

@property (nonatomic, strong) NSString *privateKeyPath;

@end

@implementation Crypto

@synthesize privateKeyPath = _privateKeyPath;

- (id)init
{
    if ((self = [super init])) {
        // assume the file name is private_key.pem
        self.privateKeyPath = [[NSBundle mainBundle] pathForResource:@"private_key"
                                                                   ofType:@"pem"];
    }
    return self;
}

- (NSString *)decryptFromCipherText:(NSString *)cipherText
{
    RSA *rsa_privateKey = NULL;
    FILE *fp_privateKey;
    int rsa_private_len;
    
    // read the private key file
    if ((fp_privateKey = fopen([self.privateKeyPath UTF8String], "r")) == NULL) {
        NSLog(@"Could not open %@", self.privateKeyPath);
        return nil;
    }
    
    if ((rsa_privateKey = PEM_read_RSAPrivateKey(fp_privateKey, NULL, NULL, NULL)) == NULL)
    {
        NSLog(@"Error loading RSA Private Key File.");
        return nil;
    }
    fclose(fp_privateKey);
    
    rsa_private_len = RSA_size(rsa_privateKey);
    
    // make sure you decode the base64 string
    NSData *decodedData = [Crypto base64DataFromString:cipherText];
    
    // plain text will be stored in this variable
    char *decrypted = (unsigned char *)malloc(rsa_private_len - 1);
    char *err = NULL;
    if (RSA_private_decrypt([decodedData length], [decodedData bytes], decrypted, rsa_privateKey, RSA_PKCS1_PADDING) == -1) {
        
        ERR_load_CRYPTO_strings();
        fprintf(stderr, "Error %s\n", ERR_error_string(ERR_get_error(), err));
        fprintf(stderr, "Error %s\n", err);
        return nil;
    }
    RSA_free(rsa_privateKey);
    
    // convert the char* to NSString
    return [NSString stringWithUTF8String:(char *)decrypted];
}

// decode the base64 string
+ (NSData *)base64DataFromString: (NSString *)string
{
    unsigned long ixtext, lentext;
    unsigned char ch, inbuf[4], outbuf[3];
    short i, ixinbuf;
    Boolean flignore, flendtext = false;
    const unsigned char *tempcstring;
    NSMutableData *theData;
    
    if (string == nil)
    {
        return [NSData data];
    }
    
    ixtext = 0;
    
    tempcstring = (const unsigned char *)[string UTF8String];
    
    lentext = [string length];
    
    theData = [NSMutableData dataWithCapacity: lentext];
    
    ixinbuf = 0;
    
    while (true)
    {
        if (ixtext >= lentext)
        {
            break;
        }
        
        ch = tempcstring [ixtext++];
        
        flignore = false;
        
        if ((ch >= 'A') && (ch <= 'Z'))
        {
            ch = ch - 'A';
        }
        else if ((ch >= 'a') && (ch <= 'z'))
        {
            ch = ch - 'a' + 26;
        }
        else if ((ch >= '0') && (ch <= '9'))
        {
            ch = ch - '0' + 52;
        }
        else if (ch == '+')
        {
            ch = 62;
        }
        else if (ch == '=')
        {
            flendtext = true;
        }
        else if (ch == '/')
        {
            ch = 63;
        }
        else
        {
            flignore = true;
        }
        
        if (!flignore)
        {
            short ctcharsinbuf = 3;
            Boolean flbreak = false;
            
            if (flendtext)
            {
                if (ixinbuf == 0)
                {
                    break;
                }
                
                if ((ixinbuf == 1) || (ixinbuf == 2))
                {
                    ctcharsinbuf = 1;
                }
                else
                {
                    ctcharsinbuf = 2;
                }
                
                ixinbuf = 3;
                
                flbreak = true;
            }
            
            inbuf [ixinbuf++] = ch;
            
            if (ixinbuf == 4)
            {
                ixinbuf = 0;
                
                outbuf[0] = (inbuf[0] << 2) | ((inbuf[1] & 0x30) >> 4);
                outbuf[1] = ((inbuf[1] & 0x0F) << 4) | ((inbuf[2] & 0x3C) >> 2);
                outbuf[2] = ((inbuf[2] & 0x03) << 6) | (inbuf[3] & 0x3F);
                
                for (i = 0; i < ctcharsinbuf; i++)
                {
                    [theData appendBytes: &outbuf[i] length: 1];
                }
            }
            
            if (flbreak)
            {
                break;
            }
        }
    }
    
    return theData;
}
@end
```

### Step 4: Usage
Refer to my [previous blog](http://jslim.net/blog/2013/01/05/rsa-encryption-in-ios-and-decrypt-it-using-php/), encrypt it and get the cipher text, so that you can test here.

```obj-c
Crypto *crypto = [[Crypto alloc] init];
NSString *plainText = [crypto decryptFromCipherText:@"Your base64 encoded cipher text here"];
```
