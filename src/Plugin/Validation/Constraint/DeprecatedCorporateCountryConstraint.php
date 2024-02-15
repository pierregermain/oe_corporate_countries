<?php

declare(strict_types=1);

namespace Drupal\oe_corporate_countries\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Deprecated country constraint.
 *
 * @Constraint(
 *   id = "DeprecatedCorporateCountry",
 *   label = @Translation("Deprecated corporate country", context = "Validation")
 * )
 */
class DeprecatedCorporateCountryConstraint extends Constraint {

  /**
   * The constraint message.
   *
   * @var string
   */
  public $message = 'The country "%value" is deprecated. Please specify a replacement.';

}
