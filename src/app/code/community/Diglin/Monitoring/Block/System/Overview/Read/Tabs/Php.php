<?php
class Diglin_Monitoring_Block_System_Overview_Read_Tabs_Php extends Mage_Adminhtml_Block_Abstract
{
    protected function _toHtml()
    {
		ob_start();
		phpinfo(-1);
		$phpinfo = ob_get_contents();
		ob_end_clean();

		preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
		$output = preg_replace('#<table#', '<table class="adminlist" align="center"', $output[1][0]);
		$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
		$output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="1" cellspacing="1" cellpadding="4" width="95%"', $output);
		$output = preg_replace('#<hr />#', '', $output);
		$output = str_replace('<div class="center">', '', $output);
		$output = str_replace('</div>', '', $output);
				
        return $output;
    }
}