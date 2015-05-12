# php-cf-service-broker

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE) [![Stories in Ready](https://badge.waffle.io/cloudfoundry-community/php-cf-service-broker.png?label=ready&title=Ready)](https://waffle.io/cloudfoundry-community/php-cf-service-broker) [![Build Status](https://travis-ci.org/cloudfoundry-community/php-cf-service-broker.svg?branch=master)](https://travis-ci.org/cloudfoundry-community/php-cf-service-broker) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/?branch=master)

Php project for creating Cloud Foundry service brokers.

# Overview

The goal is to provide a php project to quickly implement new [service brokers](http://docs.cloudfoundry.org/services/overview.html) in Cloud Foundry.

## Compatibility

* [service broker API](http://docs.cloudfoundry.org/services/api.html): 2.4
* [cf-release](https://github.com/cloudfoundry/cf-release): 192 or later
* [Pivotal CF](http://www.pivotal.io/platform-as-a-service/pivotal-cf): Expected 1.4

## Getting start rapidly

 1. Clone this repo or use `composer create-project cloudfoundry-community/php-cf-service-broker`
 2. Add your service inside [/config/services.json](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/services.json) (remove example first)
 3. Create a user by doing this in command line: `php bin/addUser.php [user_name] [password]`
 4. (optional) Change configuration inside [/config/config.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/config.yml)
 5. Create the logic inside [/src/Sphring/MicroWebFramework/ServiceBroker/DefaultServiceBroker.php](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/src/Sphring/MicroWebFramework/ServiceBroker/DefaultServiceBroker.php) (I suggest to see the [doc](http://docs.cloudfoundry.org/services/api.html), no need to follow rest url project handle it)
 6. Register your new service broker by following http://docs.cloudfoundry.org/services/managing-service-brokers.html#register-broker
 7. Service broker is ready and follow rest url given by the [doc](http://docs.cloudfoundry.org/services/api.html)


## Configure database

By default this project use an sqlite database, change to another database by modifying this file: [/config/doctrine-driver.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/doctrine-driver.yml)

## Add more service broker

You can add an infinity of service broker to do this follow these steps:

 - Add another service inside [/config/services.json](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/services.json)
 - Create a new service broker class and extend [Sphring\MicroWebFramework\ServiceBroker\AbstractServiceBroker](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/src/Sphring/MicroWebFramework/ServiceBroker/AbstractServiceBroker.php)
 - Register your new broker by adding entry in [/sphring/service-broker.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/services.json) like this:
```yaml
service.broker.[your broker name]:
     class: Sphring\MicroWebFramework\ServiceBroker\[your broker class name]
     extend: service.broker.abstract
```
 - Add in this same file a new entry in `service.broker.list`, example:
```yaml
service.broker.list:
  class: \ArrayObject
  constructor:
      1:
        ref:
          default: service.broker.default
          [service name from services.json]: service.broker.[your broker name]
```
 - Unvalidate cache by doing in command line `touch sphring/main.yml` or set cache to `false` inside [/config/config.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/config.yml)
 - Your new service broker is available

## Run as a Cloud Foundry app

 1. Create a database service which his name follow this regex: `/.*(postgres|pgsql|db|database|my|maria|oracle|oci).*/i` (**note**: add in `composer.json` other pdo driver than pdo_mysql)
 2. Push the app (run `cf push`)
 3. Bind your database service on your app
 4. Restage your app
 5. Service broker app is ready try go to `http://your_url/v2/catalog` you should see your catalog

## Run the tests

Tests use [PHPUnit](https://phpunit.de/).
Run with your command line:
`vendor/bin/phpunit --bootstrap tests\bootstrap.php --no-configuration tests`

### Add a service to test

 1. Update [services.json](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/tests/Sphring/MicroWebFramework/Resources/Sphring/services.json) inside tests configuration directory.
 2. Update [service-broker.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/tests/Sphring/MicroWebFramework/Resources/Sphring/service-broker.yml) inside tests configuration directory.
 3. Run `vendor/bin/phpunit --bootstrap tests\bootstrap.php --no-configuration tests`
