<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_corporate_countries\Kernel;

use Drupal\Tests\rdf_entity\Kernel\RdfKernelTestBase;
use Drupal\Tests\rdf_skos\Traits\SkosImportTrait;

/**
 * Abstract class for kernel tests with a test RDF country vocabulary.
 */
abstract class CorporateCountriesRdfKernelTestBase extends RdfKernelTestBase {

  use SkosImportTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'rdf_skos',
    'oe_corporate_countries',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $base_url = $_ENV['SIMPLETEST_BASE_URL'];
    $this->import($base_url, $this->sparql, 'phpunit');
    $this->enableGraph('country_test');
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $base_url = $_ENV['SIMPLETEST_BASE_URL'];
    $this->clear($base_url, $this->sparql, 'phpunit');

    parent::tearDown();
  }

  /**
   * {@inheritdoc}
   */
  protected function getTestGraphInfo(string $base_url, string $test): array {
    return [
      // Main set of test countries.
      'country_test' => [
        'uri' => "http://example.com/country/$test",
        'data' => "$base_url/modules/custom/oe_corporate_countries/tests/resources/test_countries.rdf",
      ],
      // Extra set of countries, with one duplicate and one new country.
      'country_test_extra' => [
        'uri' => "http://example.com/country_extra/$test",
        'data' => "$base_url/modules/custom/oe_corporate_countries/tests/resources/test_countries_extra.rdf",
      ],
    ];
  }

}
