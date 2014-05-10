<?php

class Hackathon_MageMonitoring_Model_Check_Logfilesize extends Hackathon_MageMonitoring_Model_Check_Abstract
{

    public function _run() {

        /** @var Hackathon_MageMonitoring_Model_Content_Renderer_Abstract $renderer */
        $renderer = $this->getContentRenderer();
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
            $this->throwPlaintextContent('No log directory');
        }
        return $this;
    }
}