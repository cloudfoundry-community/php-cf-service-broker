# Cloud Foundry PHP Service Broker

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE) [![Stories in Ready](https://badge.waffle.io/cloudfoundry-community/php-cf-service-broker.png?label=ready&title=Ready)](https://waffle.io/cloudfoundry-community/php-cf-service-broker) [![Build Status](https://travis-ci.org/cloudfoundry-community/php-cf-service-broker.svg?branch=master)](https://travis-ci.org/cloudfoundry-community/php-cf-service-broker) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/cloudfoundry-community/php-cf-service-broker/?branch=master)

Php project for creating service brokers compatible with Open Service Broker API.

This project for example can be used to quickly implement new service brokers for Kubernetes or [Cloud Foundry](http://docs.cloudfoundry.org/services/overview.html).

## Compatibility

* [Open Service Broker API](https://github.com/openservicebrokerapi/servicebroker): 2.14
    * Asynchronous operations are not supported
* Kubernetes
    * v1.10
    * Kubernetes Service Catalog: v0.1.35
* Cloud Foundry
    * [cf-release](https://github.com/cloudfoundry/cf-release): 192 or later
    * [Pivotal CF](http://www.pivotal.io/platform-as-a-service/pivotal-cf): Expected 1.4

## Quick start

 1. Clone this repo or use `composer create-project cloudfoundry-community/php-cf-service-broker`
 2. Add your service inside [/config/services.json](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/services.json) (remove example first)
 3. Create a user by doing this in command line: `php bin/addUser.php [user_name] [password]`
 4. (optional) Change configuration inside [/config/config.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/config.yml)
 5. Create the logic inside
    [/src/Sphring/MicroWebFramework/ServiceBroker/DefaultServiceBroker.php](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/src/Sphring/MicroWebFramework/ServiceBroker/DefaultServiceBroker.php)
    (I suggest to see the [doc](http://docs.cloudfoundry.org/services/api.html), no need to follow rest url project handle it)
 6. Register your new service broker
    - in Kubernetes

        by following https://svc-cat.io/docs/resources/#service-brokers

    - in Cloud Foundry

        by following http://docs.cloudfoundry.org/services/managing-service-brokers.html#register-broker

 7. Service broker is ready and follow rest url given by the [doc](http://docs.cloudfoundry.org/services/api.html)



## Configuration

### Common

See [/config/config.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/config.yml).

### Database

By default this project use an sqlite database,
    change to another database by modifying this file: [/config/doctrine-driver.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/config/doctrine-driver.yml)


## Adjust logging

See `app/app.php`.

By default this application logs only warnings to stdout.

To enable logging to file you can uncomment `$logger->pushHandler` line.

To adjust log level you can change `\Monolog\Logger::*` value.
For example, to set logging level to debug you can set the value to `\Monolog\Logger::DEBUG`.
See [Monolog logger source code](https://github.com/Seldaek/monolog/blob/1.23.0/src/Monolog/Logger.php) for details.


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

## Development environment

### Router for PHP embedded web server

```bash
cd app
php -S 0.0.0.0:8888 ../config/routes/router.php
```

### Tests

Tests use [PHPUnit](https://phpunit.de/).
Run with your command line:
`vendor/bin/phpunit --bootstrap tests\bootstrap.php --no-configuration tests`

#### Add a service to test

 1. Update [services.json](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/tests/Sphring/MicroWebFramework/Resources/Sphring/services.json) inside tests configuration directory.
 2. Update [service-broker.yml](https://github.com/cloudfoundry-community/php-cf-service-broker/blob/master/tests/Sphring/MicroWebFramework/Resources/Sphring/service-broker.yml) inside tests configuration directory.
 3. Run `vendor/bin/phpunit --bootstrap tests\bootstrap.php --no-configuration tests`


## See also

Open Service Broker API:
- https://github.com/openservicebrokerapi/servicebroker

K8s Service Catalog:
- [Service Catalog documentation in the official K8s documentation](https://v1-10.docs.kubernetes.io/docs/concepts/extend-kubernetes/service-catalog/)
- [Service Catalog documentation at the official site of K8s Service Catalog](https://svc-cat.io/docs/)
- [Service Catalog datastructures and interfaces in its source code](https://github.com/kubernetes-incubator/service-catalog/blob/v0.1.35/pkg/apis/servicecatalog/v1beta1/types.go)
- [Compatibility of Service Catalog with Open Service Broker API](https://github.com/openservicebrokerapi/servicebroker/blob/master/compatibility.md)

