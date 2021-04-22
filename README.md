# Pay.nl MPOS

- [Quick start](#quick-start)
- [Requirements](#requirements)
- [Documentation](#documentation)
---

### Quick Start

You can easily install the application via Docker.
To start the application, you have to rename `docker-compose.yml.dist` file to `docker-compose.yml` and populate it with the proper values.
By default, the app will be running in `mpos-apache-php-container` container and will be available on localhost:80
```
docker-compose up -d
```

Then, you have to create `.env` file and put next values:
- BASE_API_URL=https://rest-api.pay.nl
- AUTH_TOKEN_CODE=token
- SAFE_PAY_NL_URL=https://safe.pay.nl
- QR_PAY_NL_URL=https://qr.pisp.me
- PAY_NL_FRONTEND_URL=https://www.pay.nl
- MAKE_COOKIES_SECURE=(1 or leave empty. Value 1 means that the cookie will be processed as https-only)
- CSRF_TOKEN_LIFE_TIME=60 (csrf token lifetime in minutes)
- COOKIE_LIFETIME=1440 (cookie lifetime in minutes)
- SESSION_GC_MAXLIFETIME=1440 (time after which data will be seen as 'garbage' and potentially cleaned up in minutes)
- SESSION_COOKIE_LIFETIME (session cookie lifetime in minutes)
- COOKIE_ENCRYPTION_KEY (encryption key to protect secured cookies values (token, service id, terminal))

After that, you can install all required packages via Composer.

```
composer require --no-dev
```
For more information on how to use/install composer, please visit: [https://github.com/composer/composer](https://github.com/composer/composer)

---
### Documentation

Please visit [Documentation](/docs/README.md) page to get extra documentation, user's guide and examples.