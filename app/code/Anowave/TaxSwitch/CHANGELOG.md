# Changelog

All notable changes to this project will be documented in this file.

##[100.1.1]

### Fixed

- Fixed small cast error

##[100.1.0]

### Fixed

- Fixed a typo in XML layout

##[100.0.9]

### Added

- Added ability to set Show prices text
- Added ability to show/hide particular options from dropdown including the ability to remove Both option
- Added ability to change drodown to radio choices or vise-versa
- Minor peformance updates

##[100.0.8]

### Fixed

- Fixed a problem caused when generating translation files (bin/magento i18n:collect-phrases -o "nl_NL.csv" -m)

##[100.0.7]

### Fixed

- Removed a hardcoded IP (for testing purposes)

##[100.0.6]

### Fixed

- Removed some __constructor() parameters in Plugin/Tax.php

##[100.0.5]

### Fixed

- Fixed default EXCL. tax for precision services

##[100.0.4]

### Fixed

- More flexible header.links reference

##[100.0.3]

### Added

- Added tax_display_country session/cookie

##[4.0.9]

### Added

- Added ability to set default tax display by specifying countru in $_GET e.g. /?country=GB

##[4.0.8]

##[4.0.7]

### Added

- Added an option to allow hiding of switcher dropdown. Useful in case of automatic tax rules (groups, etc.)

##[4.0.6]

### Added

- Added form key validation

##[4.0.5]

### Changed

- Refactored cacheable="false" in favor of Page Variations

##[4.0.4]

### Added

- Ability to set customer group tax display settings

##[4.0.3]

### Added

- Added ability to translate tax messages

Incl. tax
Excl. tax
Both

##[4.0.2]

### Added

- Ability to display price label automatically

##[4.0.1]

### Fixed

- Conflict with multiple switchers on page

##[4.0.0]

### Added

- New widget to allow admin to display text in static pages depending on tax settings

##[3.0.9]

### Added

- Switchable subtotal display (in cart/checkout)

##[3.0.8]

### Added

- Switchable tax display in cart and checkout

##[3.0.7]

### Added

- Added session state for pages without products to keep tax switcher selected

### Removed

- Removed hard cache flush on ?tax_display parameter

##[2.0.2]

### Added

- Added cacheable="false" in app/code/Anowave/TaxSwitch/view/frontend/layout/default.xml to mitigate cached display and switcher not changing it's state after switch.

## [2.0.1]

### Added

- First release