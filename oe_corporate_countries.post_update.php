<?php

/**
 * @file
 * Post update functions for OpenEuropa Corporate countries module.
 */

declare(strict_types = 1);

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Install the newly defined SKOS Concept defined fields.
 */
function oe_corporate_countries_post_update_00001(): TranslatableMarkup {
  // Invalidate the container to try to ensure the new service definition gets
  // picked up.
  \Drupal::service('kernel')->invalidateContainer();
  /** @var \Drupal\rdf_skos\SkosEntityDefinitionUpdateManager $update_manager */
  $update_manager = \Drupal::service('rdf_skos.skos_entity_definition_update_manager');
  $definitions = [];
  $definitions['deprecated'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Deprecated'))
    ->setDescription(t('Whether the country is deprecated or not.'))
    ->setCardinality(1)
    ->setProvider('rdf_skos');

  $installed = $update_manager->installFieldDefinitions('skos_concept', $definitions);
  if (!$installed) {
    return t('No SKOS field definitions had to be updated.');
  }

  return t('The following SKOS field definitions have been installed: @definitions.', ['@definitions' => implode(', ', $installed)]);
}
