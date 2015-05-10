# php-cf-service-broker

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
 - Your new service broker is available

## Todo

 - Add tests
 - Follow [new spec](https://docs.google.com/document/d/12ghe1B3YPhHLGcAOgJe_1PcpDUbhaaz1RentoWepwsA/edit?usp=sharing) to hand asynchronous service broker
 - Make it Cloud Foundry ready to run as an app inside Cloud Foundry by using [cf-helper-php](https://github.com/cloudfoundry-community/cf-helper-php)
