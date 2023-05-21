#### No Index No Follow Tag Magento 2 Extension

docs : http://docs.cynoinfotech.com/noindexnofollowtag/
By: CynoInfotech Team
Platform: Magento 2 Community Edition
Email: info@cynoinfotech.com

####1 - Installation Guide

 * Download extension zip file from the account
 * Unzip the file
 * root of your Magento 2 installation and merge app folder {Magento Root}
 * folder structure --> app/code/Cynoinfotech/IndexFollow

 
####2 -  Enable Extension :  
  
  After this run below commands from Magento 2 root directory to install module.
  
 * Run :cd < your Magento install dir >
 * php bin/magento cache:disable
 * php bin/magento module:enable Cynoinfotech_IndexFollow
 * php bin/magento setup:upgrade
 * php bin/magento setup:static-content:deploy
 * rm -rf var/cache var/generation var/di var/cache /generated

####3 - Configuration

- Go to Stores -> Configuration -> Cynoinfotech ->Index Follow
- There all configuration options 


/**********************************************************/

Extension Features:

- Modify the Meta Robot tags of the category, product and CMS pages and custom URL
- Control website pages should or should not be indexed or followed by Google and other search engine crawlers
- Set noindex nofollow on the category pages and on all products of that category pages to prevent from the crawlers
- Applied in CMS pages of the website to ignore from the crawlers
- Set noindex nofollow extension for any custom URLs
- Control search engines to pass the link juice to other pages, which prevents SEO score of the store
- Prevent products from indexed and follow from crawlers
- Prevents the store content from any replicate content
- Restrict your confidential pages from the indexing
- Enable/Disable extension from the backend

Extension Information : 

Robot.txt plays an important role when Google and other search engine crawlers index the website or store. The Robots Meta tag applied in the website pages that controlling correct index and served in search results. Not need to serve all pages of the website on the search engines. In most cases, the search engine crawlers don’t need to crawl through your entire website or store. Therefore, this needs to carefully select which pages to index, follow, noindex or nofollow.

NoIndex NoFollow Magento 2 extension modify the Meta Robot tags of the category, product and CMS pages and custom URL. This extension allows you to control on which pages of the website should or should not be indexed or followed by Google or other search engine crawlers. This extension helps to control the search engines to make website content in search result pages. NoFollow control search engines to pass the link juice to other pages, which prevents SEO score of the store.

With noindex nofollow Magento 2 extension store owner can set any four Meta robot.txt combinations to controlling search engine crawlers:

- Index, Follow – Allows web crawlers to INDEX the page & continues to FOLLOW the links in the webpage.
- Noindex, Nofollow – Control search engines from INDEXING the webpage and FOLLOWING the links.
- Index, Nofollow– Allow web crawlers to INDEX the web page but NOT FOLLOW the links in the webpage.
- Noindex, Follow – Controls web crawlers from INDEXING the webpage but FOLLOW the links in the webpage.

NoIndex NoFollow Magento 2 Extension allows the store owner to prevent products from indexed and follow from Google and other search engine crawlers. Also, it allows set noindex nofollow on the category pages and on all products of that category pages to prevent from the crawlers. noindex nofollow extension applied in CMS pages of the website to ignore from the crawlers. Also, store owner set noindex nofollow extension for any custom URLs.

NoIndex NoFollow Magento 2 extension enables or disable web crawler to crawling, indexing and follow links in the web page. This extension prevents your store content from any replicate content, also to restrict your confidential pages from the indexing.

/***************************************************/

#### To request support:

Feel free to contact us via email: info@cynoinfotech.com

www.cynoinfotech.com