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

### How to add a new widget

- Clone develop branch
- Have a look at the base interface class Hackathon_MageMonitoring_Model_Widget
- Create a new class in Model/Widget/$Tab/Mywidget.php
- Extend from the nearest Abstract class to take care of boilerplate, example: cache widget => extend from Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
- Implement the remaining methods of corresponding child interface, example: cache widget => implement Hackathon_MageMonitoring_Model_Widget_CacheStat
- Override isActive() if your widget depends on certain conditions.
- Override initConfig() if your widget wants to use custom user parameters. See dashboard dummy widget for details.
- You are done. Pull requests welcome. ;)

Have a look at the dummy widgets for more detailed usage.

### How to add a new widget from another module

- In your module config add an observer that subscribes to magemonitoring_collect_widgets_$tab event.
 You do not need an actual observer class, declaration in config.xml is enough. See config.xml of this module for details.
- Now follow the same procedure as for adding a new widget, except for repo cloning part.
- Drop the new widget class into the folder your observer config publishes.

### Core Contributors

- [Sylvain Rayé](https://github.com/diglin)
- [Alexander Turiak](https://github.com/Zifius)
- [Erik Dannenberg](https://github.com/edannenberg)
- [Yaroslav Rogoza](https://github.com/Gribnik)
- [Nick Kravchuk](https://github.com/nickua)

### Status of Project

So a lot happened here in the dev branch, what changed:

- We now have generic widgets in place, the prev. CacheStats interface was a bit to specific for other use cases.
 Check Widget interface in model folder.
- Other modules can subscribe their widgets to any tab in this module. (currently only dashboard/cachestat, but could be easily all tabs if we want)
 Good for site/module specific widgets.
- Widgets now have generic ajax refresh.
- Widgets that have a collapsed init state will not render their output until opened. Good for widgets that are resource heavy.
- Widgets support generic configuration mechanism for user interaction. Frontend users can edit default collapseable state per widget now.
- Widgets have generic callback mechanism for custom buttons

Use case example:

You want to monitor a sql query specific to a magento site:

- Create new class in Model/Widget/Dashboard/Myquery.php thats extends from Widget_Abstract and implements Widget_Dashboard
- Implement getOutput(), getName(), getVersion() see Dummy widgets for examples
- Done. :)

TODO:

- Refactor other tabs to use new widget interface where it makes sense? I would say logs and system overview tabs as those might be interesting targets for adding custom widgets.
- Refresh icon should not display if widget is in collapsed state.
 The icon already gets a class 'widget-invis' added/removed when the collapsable gets toggled but does not work anymore with latest css changes.
- Create some widgets for dashboard. =) A widget that displays certain edge checks would be nice.
 For example, shop had xx user registrations today, but only x placed an order.

NICETOHAVE:

- Magento Permissions per widget, generic if possible
- Magento Permissions per tab
- Refactor widget/config.phtml. Tooltip display not working for example. It's in pretty basic shape in general, go grazy. ^^
- More config types for widget configuration, currently we only have text/checkbox. Magento date picker would be nice, select and radio too.
 (see widget/config.phtml as starting point)
- Use magento validation for generic input checking of config modal inputs

Just create an issue with the task if you want to do one of the above.