#Belgium address autocomplete by PostcodeNL Magento 2 integration extension
###MANUAL INSTALLATION >= 2.0.*
1. Unzip to app/code/Hoofdfabriek/BePostcodeNL
2. Enable the extension
    >php bin/magento setup:upgrad       
    php bin/magento module:enable Hoofdfabriek_BePostcodeNL      
    php bin/magento setup:di:compile       
    php bin/magento cache:flush        
3. Log in to adminpanel and go to Stores -> Configuration -> Hoofdfabriek Extensions -> PostcodeNL and enable Belgium address autocomplete

###COMPOSER INSTALLATION >= 2.0.*
1. Make a directory /path/to/zipfiles/;
2. Drop Hoofdfabriek-{packageversion}.zip to that folder
3. Go to magento root folder and run next command:
    >composer config repositories.hoofdfabriek_bepostcodeNL artifact /path/to/zipfiles/
4. Install the package:
    >composer require "hoofdfabriek/magento2-be-postcodenl:{packageversion}"
5. Enable the extension
     >php bin/magento setup:upgrade       
    php bin/magento module:enable Hoofdfabriek_BePostcodeNL        
    php bin/magento setup:di:compile        
    php bin/magento cache:flush     
6. Log in to adminpanel and go to Stores -> Configuration -> Hoofdfabriek Extensions -> PostcodeNL and enable Belgium address autocomplete

###UNINSTALL INSTRUCTIONS
Run next command to remove all related data from the database:      
run in commend line: php -d memory_limit=2048M bin/magento module:uninstall Hoofdfabriek_BePostcodeNL  --clear-static-content     

If installed via composer:      
rm -rf vendor/hoofdfabriek/magento2-be-postcodenl       

If installed manually:      
rm -rf app/code/Hoofdfabriek/BePostcodeNL       

