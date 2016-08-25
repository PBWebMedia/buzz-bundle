# PB Web Media Buzz bundle

Provides a Symfony bundle for the [Buzz HTTP client library](https://github.com/kriswallsmith/Buzz) created by [Kris Wallsmith](http://kriswallsmith.net/).

This bundle also adds an entry to the Symfony debug toolbar which shows the number of requests and the total request time. In the profile you can see more details about these requests like headers and response data. 

## Installation

### Install with composer

Install the buzz-bundle using composer:

```
composer require pbweb/buzz-bundle
```

then, add the bundle to your `AppKernel`:

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Pbweb\BuzzBundle\PbwebBuzzBundle(),

## Usage

```php
$browser = $this->container->get('buzz');
$response = $browser->get('http://pbwebmedia.nl');
```
           
## Configuration

You can optionally configure buzz in your `app/config.yml` file

```yaml
pbweb_buzz:
    client_timeout: 60    # Defaults to 5
    debug: %kernel.debug% # Enables the data collector in the dev environment
```


## Copyright

### Buzz

Copyright ©2010-2011 Kris Wallsmith

### Buzz bundle
Copyright ©2014-2016 PB Web Media

