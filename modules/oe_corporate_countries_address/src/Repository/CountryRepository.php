<?php

declare(strict_types = 1);

namespace Drupal\oe_corporate_countries_address\Repository;

use Drupal\address\Repository\CountryRepository as AddressCountryRepository;
use Drupal\Component\Transliteration\TransliterationInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\oe_corporate_countries\CorporateCountryRepositoryInterface;

/**
 * A country repository with definitions provided from the Publication Office.
 */
class CountryRepository extends AddressCountryRepository {

  /**
   * The corporate country repository.
   *
   * @var \Drupal\oe_corporate_countries\CorporateCountryRepositoryInterface
   */
  protected $corporateCountryRepository;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The transliteration service.
   *
   * @var \Drupal\Component\Transliteration\TransliterationInterface
   */
  protected $transliteration;

  /**
   * Creates a CountryRepository instance.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\oe_corporate_countries\CorporateCountryRepositoryInterface $corporate_country_repository
   *   The corporate country repository.
   * @param \Drupal\Component\Transliteration\TransliterationInterface $transliteration
   *   The transliteration service.
   */
  public function __construct(CacheBackendInterface $cache, LanguageManagerInterface $language_manager, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository, CorporateCountryRepositoryInterface $corporate_country_repository, TransliterationInterface $transliteration) {
    parent::__construct($cache, $language_manager);

    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
    $this->corporateCountryRepository = $corporate_country_repository;
    $this->transliteration = $transliteration;
  }

  /**
   * {@inheritdoc}
   */
  protected function loadDefinitions($locale): array {
    if (isset($this->definitions[$locale])) {
      return $this->definitions[$locale];
    }

    $cache_key = 'oe_corporate_countries_address.op_countries.' . $locale;
    if ($cached = $this->cache->get($cache_key)) {
      $this->definitions[$locale] = $cached->data;
    }
    else {
      $this->definitions[$locale] = $this->doLoadDefinitions($locale);
      $this->cache->set($cache_key, $this->definitions[$locale], CacheBackendInterface::CACHE_PERMANENT, ['countries']);
    }

    return $this->definitions[$locale];
  }

  /**
   * Does the actual loading of country definitions.
   *
   * @param string $locale
   *   The desired locale.
   *
   * @return array
   *   The country definitions.
   */
  protected function doLoadDefinitions($locale): array {
    $countries = $this->corporateCountryRepository->getCountries();

    // Bail out early if no country information has been returned.
    if (empty($countries)) {
      return [];
    }

    /** @var \Drupal\rdf_skos\SkosEntityStorage $storage */
    $storage = $this->entityTypeManager->getStorage('skos_concept');
    $entities = $storage->loadMultiple(array_keys($countries));

    $definitions = [];
    foreach ($entities as $id => $entity) {
      $translation = $this->entityRepository->getTranslationFromContext($entity, $locale);
      $definitions[$countries[$id]['alpha-2']] = $translation->label();
    }

    uasort($definitions, function (string $a, string $b) use ($locale): int {
      return $this->transliteration->transliterate($a, $locale) <=> $this->transliteration->transliterate($b, $locale);
    });

    return $definitions;
  }

}
