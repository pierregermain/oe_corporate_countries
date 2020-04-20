<?php

declare(strict_types = 1);

namespace Drupal\oe_corporate_countries;

use Drupal\Component\Serialization\Json;
use Drupal\rdf_entity\Database\Driver\sparql\ConnectionInterface;

/**
 * A corporate country repository implementation.
 */
class CorporateCountryRepository implements CorporateCountryRepositoryInterface {

  /**
   * The SPARQL database connection.
   *
   * @var \Drupal\rdf_entity\Database\Driver\sparql\ConnectionInterface
   */
  protected $sparql;

  /**
   * Instantiates a new CorporateCountryRepository object.
   *
   * @param \Drupal\rdf_entity\Database\Driver\sparql\ConnectionInterface $sparql
   *   The SPARQL database connection.
   */
  public function __construct(ConnectionInterface $sparql) {
    $this->sparql = $sparql;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountries(): array {
    // @todo this function is heavily invoked and should be cached.
    $query = <<<SPARQL
SELECT DISTINCT ?id, ?iso, ?deprecated
WHERE {
  ?id <http://www.w3.org/2004/02/skos/core#inScheme> <http://publications.europa.eu/resource/authority/country> .
  ?id a <http://www.w3.org/2004/02/skos/core#Concept> .
  ?id <http://publications.europa.eu/ontology/authority/authority-code> ?iso .
  ?id <http://publications.europa.eu/ontology/authority/deprecated> ?deprecated .
}
ORDER BY asc(?iso)
SPARQL;

    $results = $this->sparql->query($query);

    // Create a mapping between ISO 3166-1 alpha-2 and alpha-3 country codes.
    $code_mappings = $this->getIsoCodeMappings();

    $countries = [];
    foreach ($results as $item) {
      $alpha3 = $item->iso->getValue();
      // If no alpha-2 code is present, skip the value.
      if (!isset($code_mappings[$alpha3])) {
        continue;
      }

      $countries[$item->id->getUri()] = [
        'alpha-2' => $code_mappings[$alpha3],
        'alpha-3' => $alpha3,
        // The deprecated value is returned as string, so apply the same
        // conversion done in \EasyRdf\Literal\Boolean::isTrue().
        'deprecated' => $item->deprecated->getValue() === 'true' || $item->deprecated->getValue() === '1',
      ];
    }

    return $countries;
  }

  /**
   * {@inheritdoc}
   */
  public function getDeprecatedCountries(): array {
    return array_filter($this->getCountries(), function ($data): bool {
      return $data['deprecated'];
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryByIsoAlpha2(string $alpha2): ?array {
    foreach ($this->getCountries() as $id => $data) {
      if ($data['alpha-2'] === $alpha2) {
        // Add the country SKOS concept ID in the returned data.
        $data['id'] = $id;
        return $data;
      }
    }

    return NULL;
  }

  /**
   * Returns a mapping between ISO 3166-1 alpha-2 and alpha-3 country codes.
   *
   * @return array
   *   The alpha-2 codes, keyed by alpha-3.
   */
  protected function getIsoCodeMappings(): array {
    $filename = drupal_get_path('module', 'oe_corporate_countries') . '/resources/country-code-mappings.json';
    $code_mappings = Json::decode(file_get_contents($filename));

    return is_array($code_mappings) ? $code_mappings : [];
  }

}
