<?php

use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Alerts\HaliteAlert;

$plugin = sodium_encrypt_encryption_methods();

/**
 * Implements hook_encrypt_encryption_methods().
 */
function sodium_encrypt_encryption_methods() {
  return array(
    'title' => t('Sodium'),
    'description' => t('<a href="@libsodium-url">Libsodium encryption</a> via <a href="@libsodium">the Halite Library</a>. See the Sodium README.txt for details.', array(
      '@libsodium-url' => 'https://github.com/jedisct1/libsodium-php',
      '@halite-url' => 'https://github.com/paragonie/halite',
    )),
    'encrypt callback' => '_sodium_encryption_methods_sodium',
    'dependency callback' => '_sodium_check_requirements',
  );
}

/**
 * Callback for Encrypt implementation: Sodium
 *
 * Adds an encryption method that uses Libsodium for cryptographic operations.
 *
 * @EncryptionMethod(
 *   id = "sodium",
 *   title = @Translation("Sodium"),
 *   description = "Uses Libsodium for cryptographic operations.",
 *   key_type = {"encryption"}
 * )
 */
function _sodium_encryption_methods_sodium($op, $text, $key, $options = array()) {
  $disable_base64 = array_key_exists('base64', $options) && $options['base64'] == FALSE;
  // Check op.
  if ($op == 'decrypt') {
      // Check if we are disabling base64 encoding.
    if (!$disable_base64) {
       $text = base64_decode($text);
    }
    // Create the key object.
    try {
      $encryption_key = new EncryptionKey($key);
      }
      catch (InvalidKey $e) {
        return FALSE;
      }
      try {
        return Crypto::decrypt($text, $encryption_key);
      }
      catch (HaliteAlert $e) {
        return FALSE;
    }
  }
  else {
    $processed_text = '';
    // Create the key object.
      try {
        $encryption_key = new EncryptionKey($key);
      }
      catch (InvalidKey $e) {
        return FALSE;
      }
      // Encrypt.
      try {
        $processed_text = Crypto::encrypt($text, $encryption_key);
      }
      catch (HaliteAlert $e) {
        return FALSE;
      }
      // Check if we are disabling base64 encoding.
      if (!$disable_base64) {
        $processed_text = base64_encode($processed_text);
      }

    return $processed_text;
  }
}

/**
 * Callback to check requirements status
 */
function _sodium_check_requirements() {
  $errors = array();
  // Check if the libsodium php extension and the halite library are loaded
  if (!extension_loaded('libsodium')) {
    $errors[] = t('Libsodium PHP extension is not installed.');
  }
  elseif (!class_exists('\ParagonIE\Halite\Symmetric\Crypto')) {
    $errors[] = t('Halite library not installed. Check composer manager to verify the library is installed');
  }

  return $errors;
}
