# OpenEuropa Corporate countries address

This module provides integration of the EC corporate countries with the [address](https://www.drupal.org/project/address) module.

The corporate countries are integrated in the address country repository, thus they are made available in the address
and country fields.

Field constraints are applied so that deprecated corporate countries cannot be referenced for new entities or when
updating existing ones.\
Entities that are already referencing deprecated countries will be able to render correctly their values. This will ease
migrations.

## Installation

Before enabling this module, make sure that the following modules are present in your codebase by adding them to your
`composer.json`:

```bash
composer require "drupal/address ~1.8"
```
