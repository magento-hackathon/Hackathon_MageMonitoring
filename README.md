MageMonitoring
==============

Magento module to get health information of your Magento installation (Server, PHP, Database, Cache, Logs, Rewrites, Modules version installed, ...)

### License OSL v3 & MIT (for some JS files)

### Contribution

Everybody is welcome, we recommend to follow the [Firegento Git Guidelines](https://github.com/firegento/coding-guidelines/blob/master/guidelines/04_GIT.md)

### Features

- Flexible yet simple plugin framework to execute checks or other tasks
- Easily automate tasks with the provided WatchDog interface, includes aggregated reports for less spam. Get notified when your log files start moving!
- Plugins can be added from other modules via config.xml declaration
- UI fully configurable via frontend or config.xml
- Merged with [Healthcheck](https://github.com/firegento/HealthCheck)

The default plugins currently provide the following information:

- OS / Server / Memory Information / Magento version vs available
- Database Information
- PHP version and some important configuration values vs recommended
- Store configuration checks
- Modules installed, their version number, their status and some recommended extensions
- Product composition / types
- Cache statistics with option to flush each cache or all at once (APC, APCU, Memcache, Redis, ZendOpcache)
- Magento debug/exception log monitoring
- Check for class and template file rewrites
- SEO / Privacy / Security / Cron / Important files / Patches check

### Documentation

- [Plugin Dev Documentation](https://github.com/firegento/magemonitoring/tree/master/doc/PluginDev.md)

- [Example Extender Module](https://github.com/firegento/magemonitoring/tree/module/extender)

### Usage

Log into the Magento backend and navigate to: System > Monitoring AND/OR System > Configuration > Advanced > Monitoring

Installation Instructions
-------------------------

### Via modman

- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/firegento/magemonitoring/`

### Via composer
- Install [composer](http://getcomposer.org/download/)
- Install [Magento Composer](https://github.com/firegento/magento-composer-installer)
- Create a composer.json into your project like the following sample:

```json
{
    ...
    "require": {
        "firegento/magemonitoring":"*"
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
* Via modman: `modman remove magemonitoring`
* Via composer, remove the line of your composer.json related to `firegento/magemonitoring` and do `php composer.phar update`

### Core Contributors

- [Sylvain Rayé](https://github.com/diglin)
- [Alexander Turiak](https://github.com/Zifius)
- [Erik Dannenberg](https://github.com/edannenberg)
- [Yaroslav Rogoza](https://github.com/Gribnik)
- [Nick Kravchuk](https://github.com/nickua)

### Special Thanks to [Shopwerf](shopwerft.com) for their contribution
