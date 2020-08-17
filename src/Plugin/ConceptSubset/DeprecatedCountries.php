<?php

declare(strict_types = 1);

namespace Drupal\oe_corporate_countries\Plugin\ConceptSubset;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\rdf_entity\RdfFieldHandlerInterface;
use Drupal\rdf_skos\ConceptSubsetPluginBase;
use Drupal\rdf_skos\Plugin\PredicateMapperInterface;

/**
 * Creates a subset of the countries vocabulary.
 *
 * @ConceptSubset(
 *   id = "deprecated_countries",
 *   label = @Translation("Deprecated countries"),
 *   description = @Translation("Filters out deprecated countries."),
 *   predicate_mapping = TRUE,
 *   concept_schemes = {
 *     "http://publications.europa.eu/resource/authority/country"
 *   }
 * )
 */
class DeprecatedCountries extends ConceptSubsetPluginBase implements PredicateMapperInterface {

  /**
   * {@inheritdoc}
   */
  public function alterQuery(QueryInterface $query, $match_operator, array $concept_schemes = [], string $match = NULL): void {
    $query->condition('deprecated', 'false');
  }

  /**
   * {@inheritdoc}
   */
  public function getPredicateMapping(): array {
    $mapping = [];

    $mapping['deprecated'] = [
      'column' => 'value',
      'predicate' => ['http://publications.europa.eu/ontology/authority/deprecated'],
      'format' => RdfFieldHandlerInterface::NON_TYPE,
    ];

    return $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseFieldDefinitions(): array {
    $fields = [];

    $fields['deprecated'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Deprecated'))
      ->setDescription(t('Whether the country is deprecated or not.'))
      ->setCardinality(1);

    return $fields;
  }

}
