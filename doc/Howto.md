### How to add a new widget

- Clone develop branch
- Have a look at the base interface class Hackathon_MageMonitoring_Model_Widget
- Create a new class in Model/Widget/$Tab/Mywidget.php
- Extend from the nearest Abstract class to take care of boilerplate, example: cache widget => extend from Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
- Implement the remaining methods of corresponding child interface, example: cache widget => implement Hackathon_MageMonitoring_Model_Widget_CacheStat
- Override isActive() if your widget depends on certain conditions.
- Override initConfig() if your widget wants to use custom user parameters. The dashboard dummy widget has some examples.
- You are done. Pull requests welcome. ;)

Have a look at the existing widgets for more detailed usage.

### How to add a new watch dog

- The WatchDog interface is a tiny extension of the Widget interface.
- It's fine to have a widget class also contain the watch dog implementation.
- If you (already) extend from Widget_Abstract you only need to implement watch(), with a more specific abstract class it
becomes even more easier. See Model/Widget/Log/* for details.

### How to add a new widget or watch dog from another module

- In your global module config add a widgets node that declares your widget folder. See config.xml of this module for details.
- Now follow the same procedure as for adding a new widget or watch dog, except for the repo cloning part.
- Drop your shiny new class into the folder your config.xml publishes. Compare with folder structure of this module if unsure.
