<?php

declare(strict_types = 1);

namespace Drupal\oe_corporate_countries\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\oe_corporate_countries\CorporateCountryRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for the deprecated country constraint.
 */
class DeprecatedCorporateCountryConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The corporate country repository.
   *
   * @var \Drupal\oe_corporate_countries\CorporateCountryRepositoryInterface
   */
  protected $corporateCountryRepository;

  /**
   * Creates a new instance of the class.
   *
   * @param \Drupal\oe_corporate_countries\CorporateCountryRepositoryInterface $corporateCountryRepository
   *   The corporate country repository.
   */
  public function __construct(CorporateCountryRepositoryInterface $corporateCountryRepository) {
    $this->corporateCountryRepository = $corporateCountryRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('oe_corporate_countries.corporate_country_repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if ($value === NULL || $value === '') {
      return;
    }

    $info = $this->corporateCountryRepository->getCountryByIsoAlpha2($value);

    if (!empty($info) && $info['deprecated']) {
      $this->context->addViolation($constraint->message, ['%value' => $value]);
    }
  }

}
