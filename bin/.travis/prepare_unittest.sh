#!/bin/sh

# File for setting up system for unit/integration testing

# Disable xdebug to speed things up as we don't currently generate coverge on travis
if [ "$TRAVIS_PHP_VERSION" != "hhvm" ] ; then phpenv config-rm xdebug.ini
  # Disable expired certificate
. - sed -i '/mozilla\/DST_Root_CA_X3.crt/ s/./!&/' /etc/ca-certificates.conf \ && update-ca-certificates --verbose ; fi

# Update composer to newest version
# disabled, issues with packagist/github, something..
composer self-update -v --no-interaction

# Setup github key to avoid api rate limit
cp bin/.travis/composer-auth.json ~/.composer/auth.json

# Switch to another Symfony version if asked for
if [ "$SYMFONY_VERSION" != "" ] ; then composer require --no-update symfony/symfony="$SYMFONY_VERSION" ; fi;

# Install packages using composer
composer install -v --no-progress --no-interaction
