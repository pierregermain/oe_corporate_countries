<?php

declare(strict_types = 1);

namespace Drupal\oe_corporate_countries_address;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\oe_corporate_countries_address\Repository\CountryRepository;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replaces the address.country_repository service with our implementation.
 */
class OeCorporateCountriesAddressServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->has('address.country_repository')) {
      $container->getDefinition('address.country_repository')
        ->setClass(CountryRepository::class)
        ->setArguments([
          new Reference('cache.data'),
          new Reference('language_manager'),
          new Reference('entity_type.manager'),
          new Reference('entity.repository'),
          new Reference('oe_corporate_countries.corporate_country_repository'),
        ]);
    }
  }

}
