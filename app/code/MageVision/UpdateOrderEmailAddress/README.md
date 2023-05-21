# [MageVision](https://www.magevision.com/) Update Order Email Address Extension for Magento 2

## Overview
The Update Order Email Address extension gives you the ability to modify an incorrect order email address. Sometimes a customer service agent or a guest customer or a new registered customer enters accidentally an incorrect email address when submits an order.
This causes the problem that the customer never receives the order confirmation email. Unfortunately customer service cannot modify the email address on the order.
This extension solves that problem by letting you to modify the customer email address on order level and resend the order confirmation email to the new email address.

## Key Features
	* Modify the customer email address on order level
	* Resend the order confirmation email to the new email address
	
## Other Features
	* Developed by a Magento Certified Developer
	* Meets Magento standard development practices
    * Single License is valid for 1 live Magento installation and unlimited test Magento installations
	* Simple installation
	* 100% open source

## Compatibility
Magento Community Edition 2.0 - 2.1 - 2.2 - 2.3

## Installing the Extension
	* Backup your web directory and store database
	* Download the extension
		1. Sign in to your account
		2. Navigate to menu My Account → My Downloads
		3. Find the extension and click to download it
	* Extract the downloaded ZIP file in a temporary directory
	* Upload the extracted folders and files of the extension to base (root) Magento directory. Do not replace the whole folders, but merge them. If you have downloaded the extension from Magento Marketplace, then create the following folder path app/code/MageVision/UpdateOrderEmailAddress and upload there the extracted folders and files.
        * Connect via SSH to your Magento server as, or switch to, the Magento file system owner and run the following commands from the (root) Magento directory:
              1. cd path_to_the_magento_root_directory 
              2. php bin/magento maintenance:enable
              3. php bin/magento module:enable MageVision_UpdateOrderEmailAddress
              4. php bin/magento setup:upgrade
              5. php bin/magento setup:di:compile
              6. php bin/magento setup:static-content:deploy
              7. php bin/magento maintenance:disable
        * Log out from Magento admin and log in again
		
## Support
If you need support or have any questions directly related to a [MageVision](https://www.magevision.com/) extension, please contact us at [support@magevision.com](mailto:support@magevision.com)
