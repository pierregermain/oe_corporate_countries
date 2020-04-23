<?php

declare(strict_types = 1);

namespace Drupal\oe_corporate_countries;

/**
 * Interface for corporate country repositories.
 */
interface CorporateCountryRepositoryInterface {

  /**
   * Returns the list of corporate countries.
   *
   * @return array
   *   An associative array of countries information, keyed by SKOS concept ID.
   *   Each entry is an associative array with the following keys:
   *    - alpha-2: the ISO 3166-1 alpha-2 country code.
   *    - authority_code: the Publication Office authority country code.
   *    - deprecated: a boolean indicating if the country is deprecated.
   */
  public function getCountries(): array;

  /**
   * Returns the list of corporate countries that are marked as deprecated.
   *
   * @return array
   *   An associative array of countries information, keyed by SKOS concept ID.
   *   Each entry is an associative array with the following keys:
   *    - alpha-2: the ISO 3166-1 alpha-2 country code.
   *    - authority_code: the Publication Office authority country code.
   *    - deprecated: a boolean indicating if the country is deprecated.
   */
  public function getDeprecatedCountries(): array;

  /**
   * Returns a specific country data, given its ISO 3166-1 alpha-2 code.
   *
   * @param string $alpha2
   *   The country ISO 3166-1 alpha-2 code.
   *
   * @return array|null
   *   NULL if a country with the given alpha-2 code is not found. Otherwise an
   *   associative array of country information:
   *    - id: the country SKOS concept ID.
   *    - alpha-2: the ISO 3166-1 alpha-2 country code.
   *    - authority_code: the Publication Office authority country code.
   *    - deprecated: a boolean indicating if the country is deprecated.
   */
  public function getCountryByIsoAlpha2(string $alpha2): ?array;

}
