---
layout: post
title: "Remove unwanted/dummy character in C language"
date: 2013-09-25 19:22
comments: true
categories: 
- c language
---

I've encounter this issue in RSA decryption (`RSA_private_decrypt`)

```c
int result_length = RSA_private_decrypt(64, (unsigned char*)crypt_chunk, (unsigned char *)result_chunk, rsa_privateKey, RSA_PKCS1_PADDING);
printf("Result chunk: %s\nChunk length: %d\n", result_chunk, result_length);
```

Output

```
Result chunk: 33-9998-123-123408101123451250-PARADM01_00023054-CY00\240Z
Chunk length: 53
```

But what I want is `33-9998-123-123408101123451250-PARADM01_00023054-CY00`

I googled for some time still can't get the result. By try-and-error method, I solved this by adding the following code

```c
char tmp_result[result_length + 1];
strcpy(tmp_result, result_chunk);
tmp_result[result_length] = '\0';
printf("New chunk: %s\n", tmp_result);
```

Output

```
New chunk: 33-9998-123-123408101123451250-PARADM01_00023054-CY00
```

1. Declaring a new variable with additional 1 more character of the original result
2. Copy the original result to the new variable
3. Inject a `null` character to the end of the new variable

Hope this helps. :)
