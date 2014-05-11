HealthCheck
===========

The extension helps to discover configuration and technical issues within a magento installation. It's target group are developers.


## Basic Architecture

The HealthCheck provides the developer with internal information about technical issues. It implements a plugin architecture that makes it easy to execute included default checks or even create new checks. 
The checks can be executed in Magento backend, by cron, or even via web service.


## Test Plugins

Each check is build in an own plugin, which is simply a model class extending the abstract class *Hackathon_HealthCheck_Model_Check_Abstract*. Additionally it is configured in *config.xml* as described in detail below.

### Check Types

Each check can work in two basic ways:
* static: The test is directly executed on opening backend tool (used for quick tasks)
* ondemand: The test is executed on demand when user hits "execute" button (used for long-running tasks)

```xml
<type>static</type>
```

### Content Types

The plugin must return the check result as one of the following content-types configured in config.xml

* plaintext
* piechart 
* barchart 
* donutchart
* table

```xml
<content-type>true</content-type>
``` 


### Supported Magento Versions

For each plugin the supported Magento versions can be configured

```xml
 <versions> <!-- supported magento versions (optional) -->
    1.5.* <!-- all 1.5 versions -->
    1.6.* <!-- all 1.6 versions -->
    1.7.0.2 <!-- only exactly 1.7.0.2 -->
    1.8.0.* <!-- all 1.8.0.* versions -->
 </versions>
```

### Example (sitemap check)

```xml
<config>
    <global>
        <healthcheck>
            <sitemap> <!-- name of the check -->
                <model>hackathon_healthcheck/check_sitemap</model> <!-- used model class -->
                <active>true</active> <!-- activation of this plugin (true|false) -->
                <type>static</type> <!-- execution type (static|ondemand) -->
                <content-type>plaintext</content-type> <!-- content type of the plugin result -->
                <versions> <!-- supported magento versions (optional) -->
                    1.5.* <!-- all 1.5 versions -->
                    1.6.* <!-- all 1.6 versions -->
                    1.7.0.2 <!-- only exactly 1.7.0.2 -->
                    1.8.0.* <!-- all 1.8.0.* versions -->
                </versions>
            </sitemap>
        </healthcheck>
    </global>
</config>
```

## Backend Usage

We provide a dashboard in System > Healthcheck > Dashboard wich at the moment list every check and executes static-checks right away.


## Howto: Developing an own Test Plugin

To create an own check plugin you simply have to extend the abstract class *Hackathon_HealthCheck_Model_Check_Abstract* and implement the only abstract method *run()* with your check logic. Afterwards register your plugin at the HealthCheck by adding the described XML-Code to the config.xml of your module. Done!

Be sure to pass the data you create in the way the respective Display-Type-Renderer needs it. More information on this will follow here:

```php
/**
* Plaintext
* Just put in a String which contains your message/data
**/

public function throwPlaintextContent($message) {

        $factory = Mage::getModel('hackathon_healthcheck/factory');
        $this->setContentType(Hackathon_HealthCheck_Model_Content_Renderer_Plaintext::CONTENT_TYPE_PLAINTEXT);
        $this->setContentRenderer($factory->getContentRenderer($this));
        $this->getContentRenderer()->setPlaintextContent(Mage::helper('hackathon_healthcheck')->__($message));
    }
    
/**
* Table
*
* Create an array with your table headers first:
**/

 $header = array(
     $helper->__('ID'),
     $helper->__('Filename'),
     $helper->__('Path'),
     $helper->__('Status')
 );
 $this->getContentRenderer()->setHeaderRow($header);

 
/**
* Then fill up another array with your data and append it to the renderer
* You even can add css-classes via our helper to color your rows.
**/

 $status = $helper->__('Sitemap file not found');
 $warn = array('_cssClasses' =>  $helper->getConst('WARN_TYPE_ERROR'));
 // The other variables here just contain the row-information
 $row = array ($id, $filename, $totalPath, $status);
 $this->getContentRenderer()->addRow($row, $warn);

```
Now your check plugin should be visible in Magento backend and executed directly (static mode) or on click (ondemand mode). The results will be saved directyl by the HealthCheck framework.
