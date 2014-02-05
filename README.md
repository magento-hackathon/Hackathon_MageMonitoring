MageMonitoring
==============

Magento Extension to get health of your Magento installation (Server, PHP, APC, Logs, Rewrites, Modules version installed ...)

### Features

The module gathers information of the current Magento installation:

- OS / Server / Memory Information / Magento version vs available
- PHP version and some important configuration values vs recommended
- Modules installed and their version number
- Cache statistics with option to flush each cache (APC, APCU, Memcache, ZendOpcache)
- Magento debug/exception logs
- Check for class and template file rewrites
- Custom site widgets can be added from other modules via observer

### Usage

Go to the menu System > Tools > Monitoring

Installation Instructions
-------------------------

### Via modman

- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/magento-hackathon/Hackathon_MageMonitoring/`

### Via composer
- Install [composer](http://getcomposer.org/download/)
- Install [Magento Composer](https://github.com/magento-hackathon/magento-composer-installer)
- Create a composer.json into your project like the following sample:

```json
{
    ...
    "require": {
        "magento-hackathon/hackathon_magemonitoring":"*"
    },
    "repositories": [
	    {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    "extra":{
        "magento-root-dir": "./"
    }
}
```

- Then from your `composer.json` folder: `php composer.phar install` or `composer install`

### Manually
- You can copy the files from the folders of this repository to the same folders of your installation


### Installation in ALL CASES
* Clear the cache, logout from the admin panel and then login again.

Uninstallation
--------------
* Remove all extension files from your Magento installation
* Via modman: `modman remove Hackathon_MageMonitoring`
* Via composer, remove the line of your composer.json related to `magento-hackathon/hackathon_magemonitoring`

### How to add a new cache widget

- Clone develop branch
- Have a look at the interface class Hackathon_MageMonitoring_Model_Widget_CacheStat
- Implement the interface, extend from Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract to take care of boilerplate
- For extra values have a look at Hackathon_MageMonitoring_Model_Widget_CacheStat_Dummy::getOutput()
- Drop the class into Model/Widget/CacheStat
- You are done. Pull requests welcome. ;)

### How to add a custom widget from another module

- In your module add an observer that subscribes to magemonitoring_collect_widgets_cachestat/dashboard event.
  See config.xml of this module.
- Now follow the same procedure as for adding a new cache widget, exchange CacheStat with Dashboard as needed
- Drop the impl class into the folder your observer publishes.

### Core Contributors

- [Sylvain Rayé](https://github.com/diglin)
- [Alexander Turiak](https://github.com/Zifius)
- [Erik Dannenberg](https://github.com/edannenberg)
- [Yaroslav Rogoza](https://github.com/Gribnik)
- [Nick Kravchuk](https://github.com/nickua)

### Status of Project

STABLE
