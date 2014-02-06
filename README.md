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

So a lot happened here in the dev branch, what changed:

- We now have generic widgets in place, the prev. CacheStats interface was bit to specific for other use cases.
 Check Widget interface in model folder.
- Other modules can subscribe their widgets to any tab in this module. (currently only dashboard/cachestat, but could be easily all tabs if we want)
 Good for site/module specific widgets.
- Widgets now have generic ajax refresh.
- Widgets that have a collapsed init state will not render their output until opened. Good for widgets that are resource heavy.
- Widgets support generic configuration mechanism for user interaction. Frontend users can edit default collapseable state per widget now.

Use case example:

You want to monitor a sql query specific to a magento site:

- Create new class in Model/Widget/Dashboard/Myquery.php thats extends from Widget_Abstract and implements Widget
- Implement getOutput(), getName(), getVersion(), isActive()
- Done. :)

TODO:

- Support generic callback mechanism, ie widget adds a custom button and wants method x getting called when the user clicks on the button
 Should be fairly easy to implement, just add generic callback handler to controller that calls passed methodname on the widget.
- Magento Permissions per widget, generic if possible - nice to have for next release but not needed
- Magento Permissions per tab, nice to have for next release but not needed
- Javascript in widget.phtml and sub templates could use some refactoring, probably better to create generic functions that take the widgetId as parameter
- Check css/frontend for quirks, the modal for widget config is pretty basic.
- Create some widgets for dashboard. =) A widget that displays certain edge checks would be nice. For example, shop had xx user registrations today, but only x placed an order.

Just create an issue with the task if you want to do one of the above.