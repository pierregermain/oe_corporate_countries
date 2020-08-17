<?php

namespace Drupal\Tests\oe_corporate_countries\Kernel;

use Drupal\Tests\rdf_skos\Traits\SkosEntityReferenceTrait;

/**
 * Tests the corporate countries concept subsets.
 */
class CorporateCountrySubsetTest extends CorporateCountriesRdfKernelTestBase {

  use SkosEntityReferenceTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'oe_corporate_countries',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->enableGraph('country_test');
  }

  /**
   * Tests the deprecated country concept subset.
   */
  public function testDeprecatedCountryConceptSubset(): void {
    // Create a country reference field without the subset.
    $this->createSkosConceptReferenceField(
      'entity_test',
      'entity_test',
      ['http://publications.europa.eu/resource/authority/country'],
      'field_country',
      'Countries',
      NULL
    );

    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $this->container->get('entity_type.manager');
    /** @var \Drupal\entity_test\Entity\EntityTest $entity */
    $entity = $entity_type_manager->getStorage('entity_test')
      ->create(['type' => 'entity_test']);

    // Since we are not using the subset, we can reference a deprecated country.
    $entity->set('field_country', 'http://publications.europa.eu/resource/authority/country/ANT');
    $violations = $entity->field_country->validate();
    $this->assertCount(0, $violations);

    // Update the field to use the corporate countries concept subset.
    $reference_field = $entity_type_manager->getStorage('field_config')->load('entity_test.entity_test.field_country');
    $handler_settings = $reference_field->getSetting('handler_settings');
    $handler_settings['concept_subset'] = 'deprecated_countries';
    $reference_field->setSetting('handler_settings', $handler_settings);
    $reference_field->save();

    // Referencing a deprecated country triggers a validation error.
    /** @var \Drupal\entity_test\Entity\EntityTest $entity */
    $entity = $entity_type_manager->getStorage('entity_test')
      ->create(['type' => 'entity_test']);
    $entity->set('field_country', 'http://publications.europa.eu/resource/authority/country/ANT');
    $violations = $entity->field_country->validate();
    $this->assertCount(1, $violations);
    $this->assertEquals(t('This entity (%type: %id) cannot be referenced.', ['%type' => 'skos_concept', '%id' => 'http://publications.europa.eu/resource/authority/country/ANT']), $violations[0]->getMessage());

    // We can still reference non deprecated countries.
    $entity->set('field_country', 'http://publications.europa.eu/resource/authority/country/ITA');
    $violations = $entity->field_country->validate();
    $this->assertCount(0, $violations);
  }

}
