#Magento eCommerce module

##Who's it for?
Anyone with a [Buto](http://buto.tv "Online Video Platform") account

##What does it do?
Adds a widget to the TinyMCE editor used by Magento. This allows the user to search/select videos to be embedded into their content of choice.

##How do I use it?
Simply copy the files over to your magento install maintaining the directory structure set by the repo. In order to use this widget for products/categories etc you will need to enable the widgets feature for all WYSIWYG's within Magento.

##How does it work?
This module uses the [Buto API](http://docs.buto.tv "Online Video Platform") to pull down a list of your videos/embed codes. This is cached using Magento's vanilla cache system. Please remember to add your API keys to the Magento Configuration section under the label 'Buto'.

![Buto Configuration Options](https://dl.dropboxusercontent.com/u/31718905/web/buto-magento-module.png)

##MISC
Tested with:
*   PHP >= 5.3
*   Magento (Community Edition) v1.8
*   Requires cURL library

Please report bugs in the issue tracker