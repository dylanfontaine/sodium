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

/**
 * Adds an encryption method that uses the Halite PHP library.
 *
 * @EncryptionMethod(
 *   id = "halite",
 *   title = @Translation("Halite (Libsodium)"),
 *   description = "Uses Halite, which relies on Libsodium for its cryptographic operations.",
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
  public function encrypt($text, $key, $options = array()) {
    $encryption_key = new EncryptionKey($key);
    return Crypto::encrypt($text, $encryption_key, true);
  }

  /**
   * {@inheritdoc}
   */
  public function decrypt($text, $key, $options = array()) {
    $encryption_key = new EncryptionKey($key);
    return Crypto::decrypt($text, $encryption_key, true);
  }

}
