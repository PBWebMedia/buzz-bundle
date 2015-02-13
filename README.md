# Pbweb Buzz bundle

Provides a Symfony2 bundle for the Buzz HTTP browser library

# Installation

## Install with composer

Add the following line in the `"require"` section in your `composer.json`:

    "pbweb/buzz-bundle": "~1.0@dev"

then, ask composer to install it:

    composer update pbweb/buzz-bundle

finally, add the bundle to your `AppKernel`:

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Pbweb\BuzzBundle\PbwebBuzzBundle(),
                
# Configuration

You can optionally configure buzz in your `app/config.yml` file

    pbweb_buzz:
        client_timeout: 60 # Defaults to 5

# Usage

    $browser = $this->container->get('buzz');
    $response = $browser->get('http://www.pbwebmedia.nl');
