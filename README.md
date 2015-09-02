# Overview
[![Build Status](http://img.shields.io/travis/layerhq/layer-identity-token-php.svg)](https://travis-ci.org/layerhq/layer-identity-token-php)
[![Packagist version](https://img.shields.io/packagist/v/layerhq/layer-identity-token-php.svg)](https://packagist.org/packages/layerhq/layer-identity-token-php)

The code in this folder provides a PHP library for generating a Layer Identity Token.

Available on [Packagist](https://packagist.org/packages/layerhq/layer-identity-token-php).

### Installation

Add Layer Identity Token Provider to your project using Composer

```console
cd <your_project>
composer require layerhq/layer-identity-token-php
composer update
```

### Initial Setup

There are 3 environment variables you need to set up before using the code:

* `LAYER_PROVIDER_ID` - Provider ID found in the Layer Dashboard
* `LAYER_PRIVATE_KEY_ID` - Public key generated and stored in the Layer Dashboard
* `LAYER_PRIVATE_KEY` - Contents of private key associated with
the public key

All of these values are available in the **Keys** section of the Layer dashboard for your app. You can also manually set these values in the library (see details below).

### Usage

```php
$layerIdentityTokenProvider = new \Layer\LayerIdentityTokenProvider();
$identityToken = $layerIdentityTokenProvider->generateIdentityToken(USER_ID, NONCE);
```

The `generateIdentityToken` method requires 2 parameters:
* `USER_ID`:  The user ID of the user you want to authenticate.
* `NONCE`: The nonce you receive from Layer. See [docs](https://developer.layer.com/docs/guide#authentication) for more info.

By default, `LayerIdentityTokenProvider` will look at the `LAYER_PROVIDER_ID`, `LAYER_PRIVATE_KEY_ID`, and `LAYER_PRIVATE_KEY` environment variables for credentials. You can overwrite those values by uncommenting the following lines and replacing their values

```php
$layerIdentityTokenProvider->setProviderID("layer:///providers/...");
$layerIdentityTokenProvider->setKeyID("layer:///keys/...");
$layerIdentityTokenProvider->setPrivateKey("----BEGIN RSA PRIVATE KEY....");
```

### Running the sample

The composer package includes a sample inside the public folder. This sample can be easily deployed to [Heroku](#heroku).

Then send an example request to the server containing authenticate.php:

  ```console
  curl                          \
  -D -                          \
  -X POST                       \
  -d "nonce=NONCE" -d "user_id=USER_ID"   \
  http://YOUR_SERVER/authenticate.php```

Upon success, the endpoint will return a JSON object that contains a single key, `identity_token`. If the required input parameters were not provided, the endpoint will respond with "Invalid response."

Example successful response:

```console
HTTP/1.1 200 OK
Date: Thu, 13 Aug 2015 06:18:56 GMT
Server: Apache/2.4.10 (Unix) PHP/5.5.24
X-Powered-By: PHP/5.5.24
Content-Length: 519
Content-Type: text/html

{"identity_token":"..SNIP.."}
```

### Verify

You should verify the output of the signing request by visiting the **Tools** section of the [Layer dashboard](https://developer.layer.com/dashboard/).
Paste the value of the `identity_token` key you received from the output above and click `validate`. You should see "Token valid".

### Deploying Sample to Heroku

#### Automated Install

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy?template=https://github.com/layerhq/layer-identity-token-php)

#### Manual Install

You can easily deploy the php code to Heroku.

Install the [Heroku Toolbelt](https://toolbelt.heroku.com/) to start, then follow their 'Getting Started' instructions, including logging in the first time:

    % heroku login
    Enter your Heroku credentials.
    Email: youremail@example.com
    Password:
    Could not find an existing public key.
    Would you like to generate one? [Yn]
    Generating new SSH public key.
    Uploading ssh public key /Users/you/.ssh/id_rsa.pub

Inside your new directory, make sure you've created a git repository, and that your work is committed:

    % git init
    % git add .
    % git commit -m "Initial commit"

Then create a Heroku application:

    % heroku create
    Creating young-waterfall-2641... done, stack is cedar
    http://young-waterfall-2641.herokuapp.com/ | git@heroku.com:young-waterfall-2641.git
    Git remote heroku added

Before you deploy the application, you'll need to configure some environment
variables for the server to use. You can get these values from the Layer Dashboard under Authentication:

    % heroku config:set LAYER_PROVIDER_ID=yourlayerproviderid
    % heroku config:set LAYER_PRIVATE_KEY_ID=yourlayerkeyid

This code will look for the private key inside a file layer-key.pem.  You will get the private key when you generate a Authentication key in the dashboard.  You can optionally add the private key as a environment variable:

```
% heroku config:set LAYER_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----"
```

At this point, you are ready to deploy and start chatting. With Heroku, that's a
git push away:

    % git push heroku master

You'll see some text flying, and eventually some success. You should now be ready to [test the code](#running-the-sample). If the server returns an error you can peek at the logs to try to debug:

    % heroku logs

If you make any changes to your code, just commit and push them as
before:

    % git commit -am "Updating server code"
    % git push heroku master
