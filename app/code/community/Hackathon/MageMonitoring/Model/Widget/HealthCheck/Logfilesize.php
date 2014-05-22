<?php

class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Logfilesize
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_DEF_START_COLLAPSED = 1;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Logfile Size Check';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    public function getOutput()
    {
        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Barchart $renderer */
        $renderer = $block->newContentRenderer('barchart');
        $path = Mage::getBaseDir() . '/var/log/';

        if(is_dir($path) && file_exists($path))
        {
            if($handle = opendir($path))
            {
                while (($file = readdir($handle)) !== false)
                    if ($file != "." && $file != ".." && strpos($file, '.log'))
                    {
                        $filesize = filesize($path . $file);
                        // Byte to MB conversion, round to two
                        /**
                         * @TODO dynamically choose KB, MB, BG and use it correct in frontend
                         */
                        $renderer->addValue($file, number_format($filesize/1024/1024, 2));
                    }
                closedir($handle);
            }
        }
        else
        {
            $this->dump('No log directory');
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
