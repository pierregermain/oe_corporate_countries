<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_corporate_countries_address\Kernel;

use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use Drupal\oe_corporate_countries_address\Repository\CountryRepository;
use Drupal\Tests\sparql_entity_storage\Kernel\SparqlKernelTestBase;

/**
 * Tests the country repository service override.
 */
class CountryRepositoryOverrideTest extends SparqlKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'address',
    'rdf_skos',
    'oe_corporate_countries',
    'oe_corporate_countries_address',
  ];

  /**
   * Tests that the country repository service is overridden.
   */
  public function testServiceOverride(): void {
    $repository = $this->container->get('address.country_repository');
    $this->assertInstanceOf(CountryRepository::class, $repository);
    $this->assertInstanceOf(CountryRepositoryInterface::class, $repository);
  }

}
