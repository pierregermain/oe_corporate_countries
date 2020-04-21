<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_corporate_countries_address\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\oe_corporate_countries\Kernel\CorporateCountriesRdfKernelTestBase;

/**
 * Tests constrains on field types that contain countries.
 */
class FieldConstraintValidationTest extends CorporateCountriesRdfKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'address',
    'oe_corporate_countries_address',
    'entity_test',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('entity_test');
    $this->installEntitySchema('user');

    $this->container->get('module_handler')->loadInclude('user', 'install');
    user_install();
  }

  /**
   * Tests that constraints are applied to the country field type.
   */
  public function testCountryFieldType(): void {
    $this->createField('address_country');

    /** @var \Drupal\entity_test\Entity\EntityTest $entity */
    $entity = EntityTest::create();
    $this->assertCount(0, $entity->validate());

    $entity->set('field_test', 'XY');
    $violations = $entity->validate();
    $this->assertCount(1, $violations);
    $this->assertEquals('The country <em class="placeholder">&quot;XY&quot;</em> is not valid.', $violations[0]->getMessage());
    $this->assertEquals('field_test.0.value', $violations[0]->getPropertyPath());

    $entity->set('field_test', 'AN');
    $violations = $entity->validate();
    $this->assertCount(2, $violations);
    $this->assertEquals('The country "<em class="placeholder">AN</em>" is deprecated. Please specify a replacement.', $violations[0]->getMessage());
    $this->assertEquals('field_test.0.value', $violations[0]->getPropertyPath());
    $this->assertEquals('The country <em class="placeholder">&quot;AN&quot;</em> is not available.', $violations[1]->getMessage());
    $this->assertEquals('field_test.0.value', $violations[0]->getPropertyPath());
  }

  /**
   * Creates an entity_test field of the given type.
   *
   * @param string $field_type
   *   The field type.
   */
  protected function createField(string $field_type): void {
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_test',
      'entity_type' => 'entity_test',
      'type' => $field_type,
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => $this->randomMachineName(),
    ]);
    $field->save();
  }

}
