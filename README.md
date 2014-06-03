#Magento eCommerce module

##Who's it for?
Anyone with a [Buto](http://buto.tv "Online Video Platform") account

##What does it do?
Adds a widget to the TinyMCE editor used by Magento. This allows the user to search/select videos to be embedded into their content of choice.

##How Do I use it?
Simply copy the files over to your magento install maintaining the directory structure set by the repo.

##How does it work?
This repo uses the [Buto API](http://docs.buto.tv "Online Video Platform") to pull down a list of your videos/embed codes. This is cached using Magento's vanilla cache system. For this to work, please remember to add your API keys to the Magento Configuration section under the label 'Buto'.

---

##MISC
Tested with:
*   PHP >= 5.3
*   Magento (Community Edition) v1.8
*   Requires cURL library

Please feel free to report any bugs