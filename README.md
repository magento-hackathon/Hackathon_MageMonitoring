MageMonitoring
==============

Work in progress Magento Module to get a health status of Magento Configuration (Server, PHP, APC, ...)

### Features

The module gathers information of the current Magento installation:

- OS / Server / Memory Information / Magento version vs available
- PHP version and some important configuration values vs recommended
- Modules installed and their version number
- Cache statistics with option to flush each cache (APC, APCU, Memcache, ZendOpcache)
- Magento debug/exception logs
- Check for class and template file rewrites

### How to add a new cache

- Have a look at the interface class Hackathon_MageMonitoring_Model_CacheStats
- Implement the interface
- Drop the class into Model/CacheStats
- You are done. Pull requests welcome. ;)

### Core Contributors

- [Sylvain Rayé](https://github.com/diglin)
- [Alexander Turiak](https://github.com/Zifius)
- [Erik Dannenberg](https://github.com/edannenberg)
- [Yaroslav Rogoza](https://github.com/Gribnik)
- [Nick Kravchuk](https://github.com/nickua)

### Current Status of Project

In progress
