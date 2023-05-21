# Mage2 Module MultiSafepay ConnectTemplateVars

    ``multisafepay/module-connecttemplatevars``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/MultiSafepay`
 - Enable the module by running `php bin/magento module:enable MultiSafepay_ConnectTemplateVars`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require multisafepay/module-connecttemplatevars`
 - enable the module by running `php bin/magento module:enable MultiSafepay_ConnectTemplateVars`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Observer
	- payment_link_email_var > MultiSafepay\ConnectTemplateVars\Observer\Payment\LinkEmailVar


## Attributes



