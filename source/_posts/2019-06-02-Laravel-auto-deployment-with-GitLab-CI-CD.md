---
title: Laravel auto deployment with GitLab CI/CD
date: 2019-06-02 21:06:32
tags:
- laravel
- gitlab
- deployment
- nginx
---

I've been heard of continuous integration long ago, thus I decide to give it a try.
I found [the official guideline from GitLab](https://docs.gitlab.com/ee/ci/examples/laravel_with_gitlab_and_envoy/) itself, and it looks quite complicated.
End up I follow [this article](https://lorisleiva.com/laravel-deployment-using-gitlab-pipelines/).

## Challenge

I'm not going to repeat the original article, here what I wanted to share is the problems that I faced, and it took me days to try and error to make it works.

### 1. Composer install error

I've Nova installed, and it required a json file for authentication purpose.

So what I've changed _(the [original .gitlab-ci.yml](https://github.com/lorisleiva/laravel-docker/blob/master/gitlab/.gitlab-ci.deployments.yml) is from the original author)_

**.gitlab-ci.yml**

```yml
composer:
  stage: build
  cache:
    key: ${CI_COMMIT_REF_SLUG}-composer
    paths:
      - vendor/
  script:
    - cp auth.json.example auth.json
    - sed -i'.bck' "s/my-username/$NOVA_USER/g" auth.json
    - sed -i'.bck' "s/my-secret-password/$NOVA_PASS/g" auth.json
    - rm auth.json.bck
    - composer install --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts
    - cp .env.example .env
    - php artisan key:generate
...
```

What I've done is just add a few commands to replace the Nova username & password to the **auth.json** file, the **auth.json.example** look like below.

```json
{
    "http-basic": {
        "nova.laravel.com": {
            "username": "my-username",
            "password": "my-secret-password"
        }
    }
}
```

Then where's the `$NOVA_USER` & `$NOVA_PASS`? Just go to your GitLab project

Settings -> CI/CD -> Variables

![GitLab environment variables](/images/posts/2019-06-02-Laravel-auto-deployment-with-GitLab-CI-CD/gitlab-cicd-variable.png)

Then, in **config/deploy.php**, add the auth.json to the shared folder, edit the `options` section ([GitHub issue](https://github.com/lorisleiva/laravel-deployer/issues/116))

```php
<?php
return [
    // ...
    'options' => [
        // ...
        'shared_files' => [
            '.env',
            'auth.json',
        ],
    ],
    // ...
];
```

By default, the only **.env** will be symlinked to shared folder, so now make sure you copy the **auth.json** file to shared folder in your remote server.

### 2. Create a new user for deployment

For this part, I was referring to [the GitLab article](https://docs.gitlab.com/ee/ci/examples/laravel_with_gitlab_and_envoy/).

```
# Create user deployer
$ sudo adduser deployer
# Give the read-write-execute permissions to deployer user for directory /var/www
$ sudo setfacl -R -m u:deployer:rwx /var/www
# Then add to www-data group
$ sudo usermod -aG www-data deployer
# also allow this user to run as sudo
$ sudo usermod -aG sudo deployer
```

I didn't allow the whole **/var/www** to **deployer** user, so I created a project folder

```
$ sudo mkdir /var/www/mysite
$ sudo chown -R deployer:www-data /var/www/mysite
```

Now, must make sure the rsa private key store in **/home/deployer/.ssh/**

```
-rw------- 1 deployer deployer 2669 Jun  2 11:26 authorized_keys
-rw-r--r-- 1 deployer deployer  301 Jun  1 05:11 config
-rw------- 1 deployer deployer 3389 Jun  1 05:10 id_rsa_gitlab
-rw-r--r-- 1 deployer deployer 1337 Jun  2 08:56 known_hosts
```

_refer to the original article for more info_

### 3. post-autoload-dump event returned with error code 1

Another problem I've faced

```
  > post-autoload-dump: @php artisan package:discover
  Script @php artisan package:discover handling the post-autoload-dump event
  returned with error code 1
```

I'm using [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar), previously was in `require-dev`, I solve the issue by just moving from `require-dev` to `require`.

### 4. The command "which npm" failed.

I was struggling with this for hours, end up, it's due to the interactive & non-interactive shell mode, [see here for more info](https://capistranorb.com/documentation/faq/why-does-something-work-in-my-ssh-session-but-not-in-capistrano/).

Then I realised, I was installing the `npm` with [nvm](https://github.com/nvm-sh/nvm), this is install as per user.

I solved this issue by removing the `npm` & `node` from nvm

```
$ rm -rf ~/.nvm
```

and install node globally. [Refer to this article: Installing Using a PPA](https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-ubuntu-18-04)

[GitHub issue](https://github.com/lorisleiva/laravel-deployer/issues/117)

### 5. No application encryption key has been specified

```
production.ERROR: No application encryption key has been specified. {"exception":"[object] (RuntimeException(code: 0): No application encryption key has been specified. at /var/www/mysite/releases/2/vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php:44)
```

This is very obvious, the `APP_KEY` is empty. So I run

```
$ php artisan key:generate
$ php artisan config:cache
```

And I refresh the browser, still getting the same error. I even tried in [tinker](https://github.com/laravel/tinker), and able to get result by

```
>>> echo config('app.key');
```

Finally, I solve it by restarting fpm

```
$ sudo service php7.3-fpm restart
```

### 6. After deployed, server still serve the old revision

Update your nginx config

```
location ~ .php$ {
    try_files $uri =404;
    fastcgi_split_path_info ^(.+.php)(/.+)$;
    fastcgi_pass   unix:/var/run/php/php7.3-fpm.sock;
    fastcgi_index  index.php;
    include        fastcgi_params;

    # add this 2 line after `include`
    fastcgi_param  SCRIPT_FILENAME  $realpath_root$fastcgi_script_name;
    fastcgi_param  DOCUMENT_ROOT $realpath_root;
    ...
}
```

Then restart nginx

[See the explaination here](https://serverfault.com/questions/848503/nginx-caching-symlinks/848526#848526)

Hope this article help :)
