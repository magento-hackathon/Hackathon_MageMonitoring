MageMonitoring Plugin Documentation
===================================

- All plugins need to implement Hackathon_MageMonitoring_Model_Widget
- Extending from Hackathon_MageMonitoring_Model_Widget_Abstract, or childs extending from it, is highly recommended

### Hello Widget

-Clone module/extender branch
-Create a new file Model/Widget/Hello.php with the following contents:

```php
class Hackathon_MageMonitoringExt_Model_Widget_Hello
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    public function getName()
    {
        return 'Hello Widget';
    }

    public function getOutput()
    {
        $this->dump('Hello World!');
        return $this->_output;
    }
}
```

- Install/Symlink module
- Go to System -> Monitoring and hit Tab Config, then pull the Hello Widget into a tab of your choice and save.
- Grats, you just created your first MageMonitoring widget :)

### Fancy Output

- $this->_output[] will take any standard Magento block, but MageMonitoring provides two blocks you can use:

```php
    public function getOutput()
    {
        // using a monitoring block
        $monitoringBlock = $this->newMonitoringBlock();
        // add content to $b
        $chartDataPie = array(array('value' => 34, 'color' => '#FF0000'),
                              array('value' => 12, 'color' => '#00FF00'),
                              array('value' => 42, 'color' => '#0000FF')
                             );
        $chart = $monitoringBlock->newChartArray('my_pie', $chartDataPie);
        $monitoringBlock->addRow('warning', 'Pie Levels', '', $chart);
        // add block to output
        $this->_output[] = $monitoringBlock;

        // using a multi block - example for a table, check Model/Widget/HealthCheck/ for more usage examples
        $multiBlock = $this->newMultiBlock();
        $renderer = $multiBlock->newContentRenderer('table');
        // add content to $c
        $header = array(
                'Col A',
                'Col B',
                'Status',
        );
        $renderer->setHeaderRow($header);

        $renderer->addRow(array('Val A', 'Val B', 'snafu'));
        // add block to output
        $this->_output[] = $multiBlock;

        return $this->_output;
    }
```

### User configurable parameters

- Add to Hello.php:

```php
    // config key in db
    const CONFIG_MY_PARAM = 'hello_param';
    // define a default value
    protected $_DEF_MY_PARAM = 42;

    public function initConfig()
    {
        // call parent to create default config (collapseable state, etc)
        parent::initConfig();

        // ...and add our own
        $this->addConfig(self::CONFIG_MY_PARAM,
                        'My Label',
                        $this->_DEF_MY_PARAM,
                        'widget', // scope: widget | global
                        'text', // type: text | checkbox
                        false, // required?
                        'A wild tooltip appears.');

        return $this->_config;
    }
```

This will add a parameter MY_PARAM to your widget that can be edited by clicking on the gear icon of the widget when displayed in frontend.
To access the value from anywhere within your widget:

```php
    $this->getConfig(self::CONFIG_MY_PARAM);
```

Config types are bit limited at the moment, but should be easily extendable. Pull requests welcome on develop branch. ;)

### Buttons and Callbacks

Modify Hello.php:

```php
    // the callback method
    public function helloCallback ()
    {
        // hard work
        sleep(3);
        return Mage::helper('magemonitoring')->__('Pleased to inform you that the operation was indeed a great success! <br/> Now let me refresh that widget for you..');
    }

    public function getOutput()
    {
        // create a monitoring block
        $monitoringBlock = $this->newMonitoringBlock();

        // add a button that will execute helloCallback() when clicked. callback method needs to be in this class.
        $monitoringBlock->addButton($this, // pass widget
                                    'b_callback', // button id
                                    'click me!',  // label
                                    self::CALLBACK.'helloCallback', // pass callback name
                                    array('refreshAfter' => true),  // additional params, optional
                                    'Long running operation! You sure?'); // confirm dialog before executing, optional

        // just a normal button that links to a standard magento route
        $monitoringBlock->addButton($this, 'b_id', 'dont click me!' , '*/*/route', null, 'Last chance!');

        $this->_output[] = $monitoringBlock;
        return $this->_output;
    }
```

### Task Automation

- Implement Hackathon_MageMonitoring_Model_WatchDog
- Extending from Hackathon_MageMonitoring_Model_Widget_Abstract is highly recommended

```php
class Hackathon_MageMonitoringExt_Model_Widget_Hello
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget, Hackathon_MageMonitoring_Model_WatchDog
{
    public function watch() {
        if (true) { // check something and add to global watch dogs report if we got something to report
            $this->addReportRow('warning', 'some label', 'some warning');
        }
        return $this->_report;
    }

    ...
```php

### Creating Custom Tabs on Module Installation

To make our hello widget appear in the frontend when we install our module edit config.xml:

```xml
    <default>
        <!-- Insert a new tab with our hello widget -->
        <magemonitoring>
            <tabs>
                <my_tab>
                    <label>My Tab</label>
                    <title>My Tab</title>
                    <display_prio>500</display_prio>
                    <widgets>
                      <hello_widget>
                        <impl>Hackathon_MageMonitoringExt_Model_Widget_Hello</impl>
                      </hello_widget>
                    </widgets>
                </my_tab>
            </tabs>
        </magemonitoring>
    </default>
```

To display a generic widget with your config (in which case this xml configuraton is all you need):

```xml
    <default>
        <!-- Insert a new tab with generic tail widget that watches our custom log file -->
        <magemonitoring>
            <tabs>
                <my_tab>
                    <label>My Tab</label>
                    <title>My Tab</title>
                    <display_prio>500</display_prio>
                    <widgets>
                      <my_log>
                        <impl>Hackathon_MageMonitoring_Model_Widget_Log_Tail</impl>
                        <title>My Log</title>
                        <file_path>module/path/file.log</file_path>
                        <color>warning</color>
                        <collapsed>1</collapsed>
                        <display_prio>10</display_prio>
                      </my_log>
                    </widgets>
                </my_tab>
            </tabs>
        </magemonitoring>
    </default>
```
