<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_corporate_countries\Kernel;

use Drupal\Tests\rdf_entity\Kernel\RdfKernelTestBase;
use Drupal\Tests\rdf_skos\Traits\SkosImportTrait;

/**
 * Tests the corporate country repository.
 *
 * @coversDefaultClass \Drupal\oe_corporate_countries\CorporateCountryRepository
 */
class CorporateCountryRepositoryTest extends RdfKernelTestBase {

  use SkosImportTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'rdf_skos',
    'oe_corporate_countries',
  ];

  /**
   * Tests the getCountries() method.
   *
   * @covers ::getCountries
   */
  public function testGetCountries(): void {
    $country_repository = $this->container->get('oe_corporate_countries.corporate_country_repository');
    $corporate_countries = $country_repository->getCountries();

    $this->assertCount(272, $corporate_countries);
    // Countries not present in the country code mappings are removed.
    $this->assertArrayNotHasKey('http://publications.europa.eu/resource/authority/country/AFI', $corporate_countries);

    $this->assertEquals([
      'alpha-2' => 'IT',
      'alpha-3' => 'ITA',
      'deprecated' => FALSE,
    ], $corporate_countries['http://publications.europa.eu/resource/authority/country/ITA']);
    $this->assertEquals([
      'alpha-2' => 'AN',
      'alpha-3' => 'ANT',
      'deprecated' => TRUE,
    ], $corporate_countries['http://publications.europa.eu/resource/authority/country/ANT']);
  }

  /**
   * Tests the getDeprecatedCountries() method.
   *
   * @covers ::getDeprecatedCountries
   */
  public function testGetDeprecatedCountries(): void {
    $country_repository = $this->container->get('oe_corporate_countries.corporate_country_repository');
    $deprecated_countries = $country_repository->getDeprecatedCountries();

    $this->assertCount(23, $deprecated_countries);
    $this->assertArrayHasKey('http://publications.europa.eu/resource/authority/country/ANT', $deprecated_countries);
    $this->assertArrayNotHasKey('http://publications.europa.eu/resource/authority/country/ITA', $deprecated_countries);
  }

  /**
   * Tests the getCountryByIsoAlpha2() method.
   *
   * @covers ::getCountryByIsoAlpha2
   */
  public function testGetCountryByIsoAlpha2(): void {
    $country_repository = $this->container->get('oe_corporate_countries.corporate_country_repository');

    $this->assertNull($country_repository->getCountryByIsoAlpha2('ZZ'));
    $this->assertEquals([
      'alpha-2' => 'IT',
      'alpha-3' => 'ITA',
      'deprecated' => FALSE,
      'id' => 'http://publications.europa.eu/resource/authority/country/ITA',
    ], $country_repository->getCountryByIsoAlpha2('IT'));
    $this->assertEquals([
      'alpha-2' => 'AN',
      'alpha-3' => 'ANT',
      'deprecated' => TRUE,
      'id' => 'http://publications.europa.eu/resource/authority/country/ANT',
    ], $country_repository->getCountryByIsoAlpha2('AN'));
  }

}
