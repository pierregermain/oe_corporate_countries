<?php

declare(strict_types=1);

namespace Drupal\Tests\oe_corporate_countries\Kernel;

/**
 * Tests the corporate country repository.
 *
 * @coversDefaultClass \Drupal\oe_corporate_countries\CorporateCountryRepository
 */
class CorporateCountryRepositoryTest extends CorporateCountriesRdfKernelTestBase {

  /**
   * Tests the getCountries() method.
   *
   * @covers ::getCountries
   */
  public function testGetCountries(): void {
    $country_repository = $this->container->get('oe_corporate_countries.corporate_country_repository');

    // Only five countries are available in the configured test graph.
    $expected = [
      'http://publications.europa.eu/resource/authority/country/1A0' => [
        'alpha-2' => 'XK',
        'authority_code' => '1A0',
        'deprecated' => FALSE,
      ],
      'http://publications.europa.eu/resource/authority/country/ALA' => [
        'alpha-2' => 'AX',
        'authority_code' => 'ALA',
        'deprecated' => FALSE,
      ],
      'http://publications.europa.eu/resource/authority/country/ANT' => [
        'alpha-2' => 'AN',
        'authority_code' => 'ANT',
        'deprecated' => TRUE,
      ],
      'http://publications.europa.eu/resource/authority/country/BEL' => [
        'alpha-2' => 'BE',
        'authority_code' => 'BEL',
        'deprecated' => FALSE,
      ],
      'http://publications.europa.eu/resource/authority/country/FQ0' => [
        'alpha-2' => 'TF',
        'authority_code' => 'FQ0',
        'deprecated' => FALSE,
      ],
      'http://publications.europa.eu/resource/authority/country/ITA' => [
        'alpha-2' => 'IT',
        'authority_code' => 'ITA',
        'deprecated' => FALSE,
      ],
    ];
    $this->assertSame($expected, $country_repository->getCountries());

    // Enable another graph, which contains a new country and a duplicate.
    $this->enableGraph('country_test_extra');
    // Clear the static cache of the handler.
    $this->container->get('rdf_skos.sparql.graph_handler')->clearCache();

    // An extra country is now returned.
    $expected['http://publications.europa.eu/resource/authority/country/ROU'] = [
      'alpha-2' => 'RO',
      'authority_code' => 'ROU',
      'deprecated' => FALSE,
    ];
    $this->assertSame($expected, $country_repository->getCountries());
  }

  /**
   * Tests the getDeprecatedCountries() method.
   *
   * @covers ::getDeprecatedCountries
   */
  public function testGetDeprecatedCountries(): void {
    $country_repository = $this->container->get('oe_corporate_countries.corporate_country_repository');
    $this->assertEquals([
      'http://publications.europa.eu/resource/authority/country/ANT' => [
        'alpha-2' => 'AN',
        'authority_code' => 'ANT',
        'deprecated' => TRUE,
      ],
    ], $country_repository->getDeprecatedCountries());
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
      'authority_code' => 'ITA',
      'deprecated' => FALSE,
      'id' => 'http://publications.europa.eu/resource/authority/country/ITA',
    ], $country_repository->getCountryByIsoAlpha2('IT'));
    $this->assertEquals([
      'alpha-2' => 'AN',
      'authority_code' => 'ANT',
      'deprecated' => TRUE,
      'id' => 'http://publications.europa.eu/resource/authority/country/ANT',
    ], $country_repository->getCountryByIsoAlpha2('AN'));
  }

}
