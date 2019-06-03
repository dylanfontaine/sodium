Drupal 7.x Sodium Module - README

## Overview

The Sodium module for Drupal provides an encryption method for the Encrypt
module that allows symmetric encryption and decryption of data using the
Sodium (libsodium) software library. PHP integration is provided by the
Paragonie/Halite library.

## Drupal 7 - Sodium Requirements

* PHP 5.6 or later
* [Sodium (libsodium) library](https://github.com/jedisct1/libsodium)
* [Libsodium PHP extension](https://github.com/jedisct1/libsodium-php)
* [Halite PHP library](https://github.com/paragonie/halite)
* [Encrypt module](https://www.drupal.org/project/encrypt)

## RECOMMENDED MODULES
* [Key module](https://www.drupal.org/project/key) - Allows creating a key file with a custom path and file name. The encrypt module hard codes the key's file name to encrypt_key.key.

Whew! That's a lot of requirements.

It sounds more complicated than it actually is. Information about installing
the Libsodium library and the Libsodium PHP extension can be found in
["Using Libsodium in PHP Projects."](https://paragonie.com/book/pecl-libsodium)

The Halite PHP library can be installed using Composer Manager. The composer manager module has the option to customize the path where vendor files are loaded. This is useful if your prod server does not allow access out to d.o. or packagist.
  * To utilize composer manager, simply run "composer update --no-dev --no-ansi --optimize-autoloader" or similar to fit your environment.

## Using Sodium in Encrypt

Once everything is installed and operational, do the following:

1. Generate a random 256-bit key
  * Option 1: Output your key to a file using a method such as the following:
    * `dd if=/dev/urandom bs=32 count=1 > /path/to/file_name.key`
        (change the path and filename to suit your needs. Note: If your are not using the "Key" module, the "Encrypt" module hard codes the file name to "encrypt_key.key")
  * Option 2: Output your key to standard output and Base64-encode it so it
     can be copied and pasted:
    * `dd if=/dev/urandom bs=32 count=1 | base64 -i -`

2. OPTIONAL, but recommended. Enable and configure the [Key module](https://www.drupal.org/project/key). The Key module allows for a custom path AND file name.
  * Go to /admin/config/system/keys/add
  * Select "Encryption" for the key type
  * Select "256" for the key size
  * Select your preferred key provider
    * Select "File" if you output your key to a file in the previous step;
  do not check "Base64-encoded" unless you Base64-encoded the key
    * Select "Configuration" if you copied your key, rather than outputing to a file ("Configuration" is fine for development and testing, but please use something more secure in a production environment); paste the key value and check "Base64-encoded"
  * Click "Save key"

3. Create an encryption profile using the Encrypt module (at
   /admin/config/system/encrypt/add)
  * Select "Sodium" for the encryption method
  * Select the "File" option or if you are utilizing the "Key" module, select "Key module"
  * Click "Save Configuration"

