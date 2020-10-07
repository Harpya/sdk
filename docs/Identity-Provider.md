# Identity Provider

## Introduction

The Identity Provider (I.P.) manages the users and it's authentication lifecycle. Essentially wraps up the main functions (signup, login and recover password), and keep user's data isolated from rest of application. 

I.P. is not a library, but a microservice. Was originally designed to be used with Containers (Docker / Kubernetes technology), but is possible install and configure all components using VMs or even bare-metal. 

I.P. Can be shared among different applications within same organization, keeping credentials centralized and secure. 

Future improvements are expected, such as:
- 2FA
- OAUTH 2
- SAML
- Audit trail report
 


### Flow


When ``User`` visit some URL of ``Application A``, which expects to be authenticated, the application will check if have a valid and active session. If not, SDK performs a initial request to I.P., informing the ``User`` data, and redirects them to I.P.. I.P. will verify the ``User``, and display a login page, with options to signup and recover password. Once ``User`` informs the correct e-mail and password, I.P. will notify ``Application A`` that the authentication was successful, and redirect ``User`` to landing-page. If this ``User`` does not have profile, can signup, to then login.

## Setup

Create a new entry for your application in your identity-provider server.


## Configuration

In your application, add these values in your file ``.env``:   

```.env
HSDK_IP_INTERNAL_URL=https://auth.mydomain.com
HSDK_IP_EXTERNAL_URL=http://<identity-provider-IP-port>

HSDK_APP_ID=<application-id>
HSDK_APP_SECRET=<application-secret>
```


## Usage


Application



```PHP
use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\SDK\IdentityProvider\Broker;



```