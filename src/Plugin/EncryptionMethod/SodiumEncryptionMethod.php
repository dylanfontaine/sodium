<?php

namespace Drupal\sodium\Plugin\EncryptionMethod;

use Drupal\encrypt\EncryptionMethodInterface;
use Drupal\encrypt\Exception\EncryptException;
use Drupal\encrypt\Plugin\EncryptionMethod\EncryptionMethodBase;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Alerts\HaliteAlert;

/**
 * Adds an encryption method that uses Libsodium for cryptographic operations.
 *
 * @EncryptionMethod(
 *   id = "sodium",
 *   title = @Translation("Sodium"),
 *   description = "Uses Libsodium for cryptographic operations.",
 *   key_type = {"encryption"}
 * )
 */
class SodiumEncryptionMethod extends EncryptionMethodBase implements EncryptionMethodInterface {

  /**
   * {@inheritdoc}
   */
  public function checkDependencies($text = NULL, $key = NULL) {
    $errors = array();

    if (!class_exists('\ParagonIE\Halite\Symmetric\Crypto')) {
      $errors[] = t('Halite PHP library is not installed.');
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function encrypt($text, $key) {
    // Create the key object.
    try {
      $encryption_key = new EncryptionKey($key);
    }
    catch (InvalidKey $e) {
      return FALSE;
    }

    // Encrypt the data.
    try {
      $encrypted_data = Crypto::encrypt($text, $encryption_key, TRUE);
    }
    catch (HaliteAlert $e) {
      throw new EncryptException($e);
    }

    return $encrypted_data;
  }

  /**
   * {@inheritdoc}
   */
  public function decrypt($text, $key) {
    // Create the key object.
    try {
      $encryption_key = new EncryptionKey($key);
    }
    catch (InvalidKey $e) {
      return FALSE;
    }

    // Decrypt the data.
    try {
      $decrypted_data = Crypto::decrypt($text, $encryption_key, TRUE);
    }
    catch (HaliteAlert $e) {
      throw new EncryptException($e);
    }

    return $decrypted_data;
  }

}
