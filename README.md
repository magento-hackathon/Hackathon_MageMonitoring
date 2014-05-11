MageMonitoring
==============

Magento Extension to get health of your Magento installation (Server, PHP, Cache, Logs, Rewrites, Modules version installed ...)

### License OSL v3

### Features

The module gathers information of the current Magento installation:

- OS / Server / Memory Information / Magento version vs available
- PHP version and some important configuration values vs recommended
- Modules installed and their version number and status
- Cache statistics with option to flush each cache or all at once (APC, APCU, Memcache, Redis, ZendOpcache)
- Magento debug/exception logs
- Check for class and template file rewrites
- Generic watch dogs with aggregated reports for less spam. Get notified when your log files start moving.
- Custom site widgets or watch dogs can be added from other modules via config.xml declaration.
- Integration of the healthcheck module [Healthcheck](https://github.com/magento-hackathon/HealthCheck)

### Documentation

The documentation is available into the doc folder [Hackathon Monitoring Documentation](https://github.com/magento-hackathon/Hackathon_MageMonitoring/tree/master/doc)


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

### Core Contributors

- [Sylvain Rayé](https://github.com/diglin)
- [Alexander Turiak](https://github.com/Zifius)
- [Erik Dannenberg](https://github.com/edannenberg)
- [Yaroslav Rogoza](https://github.com/Gribnik)
- [Nick Kravchuk](https://github.com/nickua)

### Status of the project

So a lot happened here in the dev branch, what changed:

- We now have generic widgets in place, the prev. CacheStats interface was a bit to specific for other use cases.
 Check Widget interface in model folder.
- Other modules can subscribe their widgets to any tab in this module. (currently only dashboard/cachestat, but could be easily all tabs if we want)
 Good for site/module specific widgets.
- Widgets now have generic ajax refresh.
- Widgets that have a collapsed init state will not render their output until opened. Good for widgets that are resource heavy.
- Widgets support generic configuration mechanism for user interaction. Frontend users can edit default collapseable state and display priority per widget now.
- Widgets have generic callback mechanism for custom buttons
- Added WatchDog interface, widgets can now provide true monitoring via cron. Aggregated report mails to avoid spam.

Use case example:

You want to monitor a sql query specific to a magento site:

- Create new class in Model/Widget/System/Myquery.php thats extends from Widget_Abstract and implements Widget_System
- Implement getOutput(), getName(), getVersion() see Dummy widgets for examples
- Done. :)

NICETOHAVE:

- Magento Permissions per widget, generic if possible
- Magento Permissions per tab
- Refactor widget/config.phtml. Tooltip display not working for example. It's in pretty basic shape in general, go grazy. ^^
- More config types for widget configuration, currently we only have text/checkbox. Magento date picker would be nice, select and radio too.
 (see widget/config.phtml as starting point)
- Use magento validation for generic input checking of config modal inputs

Just create an issue with the task if you want to do one of the above.