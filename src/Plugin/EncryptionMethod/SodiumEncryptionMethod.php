<?php

namespace Drupal\sodium\Plugin\EncryptionMethod;

use Drupal\encrypt\EncryptionMethodInterface;
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
    $encrypted_data = FALSE;

    // Create the key object.
    try {
      $encryption_key = new EncryptionKey($key);
    }
    catch (InvalidKey $e) {
      drupal_set_message($this->t('Encryption failed because the key is not the correct size.'), 'error');
      return FALSE;
    }

    // Encrypt the data.
    try {
      $encrypted_data = Crypto::encrypt($text, $encryption_key, TRUE);
    }
    catch (HaliteAlert $e) {
      drupal_set_message($this->t('Encryption failed due to an unknown error.'), 'error');
    }

    return $encrypted_data;
  }

  /**
   * {@inheritdoc}
   */
  public function decrypt($text, $key) {
    $decrypted_data = FALSE;

    // Create the key object.
    try {
      $encryption_key = new EncryptionKey($key);
    }
    catch (InvalidKey $e) {
      drupal_set_message($this->t('Decryption failed because the key is not the correct size.'), 'error');
      return FALSE;
    }

    // Decrypt the data.
    try {
      $decrypted_data = Crypto::decrypt($text, $encryption_key, TRUE);
    }
    catch (HaliteAlert $e) {
      drupal_set_message($this->t('Decryption failed due to an unknown error.'), 'error');
    }

    return $decrypted_data;
  }

}
