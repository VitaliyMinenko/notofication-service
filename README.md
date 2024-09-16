# Notifiaer service
#### Version 1.0b
#### Author: Vitalii Minenko

Simple application for notify users with different channels and providers.

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Install all dependencies `composer install`
4. Run `docker compose up -d`
5. Copy `env.example` to `.env` and set all variables with providers instructions below.
6. You can use next route for init notify process. Provider allow  only at [sms,email,telegram]

```
curl --location 'https://localhost/api/v1/notification/' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "s.goodman@gmail.com",
    "notification": "Better call Saul.",
    "provider": "email"
}
'
```

## Env Variables

* ```TWILIO_PHONE_NUMBER=13305775127``` Phone number from twilio provider
* ```TWILIO_ACCOUNT_SID=secret``` Twilio account sid.
* ```TWILIO_AUTH_TOKEN=secret``` Twilio token
* ```MAILER_DSN=smtp://9aec0937b3b822:dbfb3e97e83eea@sandbox.smtp.mailtrap.io:2525``` Maile DSN
* ```TELEGRAM_TOKEN=secret``` Telegram token haw to use read below (https://core.telegram.org/api)
* ```TELEGRAM_URL=https://api.telegram.org/bot%s/sendMessage``` Telegram url
* ```SMS_KEY="secret"``` //Key for VONAGE provider (https://developer.vonage.com/en/documentation)
* ```SMS_SECRET="secret""``` //Secret for VONAGE provider 
* ```SMS_ORGANIZATION="GO"``` //Organization name for VONAGE provider 
* ```MULTIPLE_SEND_SMS=false``` Configuration for multiple sender accept
* ```MULTIPLE_SEND_EMAIL=false``` Enable/Disable provider from multiple
* ```MULTIPLE_SEND_TELEGRAM=true``` Enable/Disable provider from multiple
* ```MULTIPLE_SEND=false``` Enable/Disable provider from multiple
  
  
  

**Now application is ready and you can use it and test it by Postman or another service. Please enjoy ;) Thank you!**

