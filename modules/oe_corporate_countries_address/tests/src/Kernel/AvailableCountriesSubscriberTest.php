<?php

declare(strict_types=1);

namespace Drupal\Tests\oe_corporate_countries_address\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\oe_corporate_countries\Kernel\CorporateCountriesRdfKernelTestBase;

/**
 * Tests the AvailableCountriesSubscriber class.
 */
class AvailableCountriesSubscriberTest extends CorporateCountriesRdfKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'entity_test',
    'address',
    'oe_corporate_countries_address',
  ];

  /**
   * Tests the deprecated countries are removed from the countries array.
   */
  public function testGetAvailableCountriesArray(): void {

    $entityTypeName = 'entity_test';

    FieldStorageConfig::create([
      'field_name' => 'field_country',
      'entity_type' => 'entity_test',
      'type' => 'address_country',
    ])->save();

    FieldConfig::create([
      'label' => 'My field',
      'field_name' => 'field_country',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
    ])->save();

    // Get the actual countries from field.
    // This shouldn't have the deprecated countries.
    $entity = EntityTest::create();
    $actual_countries = $entity->get('field_country')
      ->appendItem()
      ->getAvailableCountries();

    // These are the expected countries we want to check for.
    $expected_countries = [
      'XK' => 'XK',
      'AX' => 'AX',
      'BE' => 'BE',
      'TF' => 'TF',
      'IT' => 'IT',
    ];

    // Assert explicitly that the actual countries format is correctly formated.
    foreach ($actual_countries as $key => $value) {
      $this->assertArrayHasKey($key, $expected_countries, 'Key ' . ' has been found');
    }

    // Assert explicitly that the actual countries array is correctly keyed.
    $expected_keys = array_keys($expected_countries);
    $actual_keys = array_keys($actual_countries);
    sort($expected_keys);
    sort($actual_keys);
    $this->assertEquals($expected_keys, $actual_keys, 'The actual countries array is correctly keyed');

    // Assert explicitly actual countries against a deprecated country.
    $this->assertArrayNotHasKey('AN', $actual_countries, 'The deprecated country "AN" is not present in actual_countries array');
  }

}
