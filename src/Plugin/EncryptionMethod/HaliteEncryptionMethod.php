<?php

/**
 * @file
 * Contains \Drupal\halite\Plugin\EncryptionMethod\HaliteEncryptionMethod.
 */

namespace Drupal\halite\Plugin\EncryptionMethod;

use Drupal\encrypt\EncryptionMethodInterface;
use Drupal\encrypt\Plugin\EncryptionMethod\EncryptionMethodBase;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Alerts as CryptoException;

/**
 * Adds an encryption method that uses the Halite PHP library.
 *
 * @EncryptionMethod(
 *   id = "halite",
 *   title = @Translation("Halite (Libsodium)"),
 *   description = "Uses Halite, which relies on Libsodium for operations.",
 *   key_type = {"encryption"}
 * )
 */
class HaliteEncryptionMethod extends EncryptionMethodBase implements EncryptionMethodInterface {

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
    catch (CryptoException\InvalidKey $e) {
      drupal_set_message($this->t('Encryption failed because the key is not the correct size.'), 'error');
      return FALSE;
    }
    
    // Encrypt the data.
    try {
      $encrypted_data = Crypto::encrypt($text, $encryption_key, TRUE);
    }
    catch (CryptoException\HaliteAlert $e) {
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
    } catch (CryptoException\InvalidKey $e) {
      drupal_set_message($this->t('Decryption failed because the key is not the correct size.'), 'error');
      return FALSE;
    }

    // Decrypt the data.
    try {
      $decrypted_data = Crypto::decrypt($text, $encryption_key, TRUE);
    }
    catch (CryptoException\HaliteAlert $e) {
      drupal_set_message($this->t('Decryption failed due to an unknown error.'), 'error');
    }

    return $decrypted_data;
  }

}
