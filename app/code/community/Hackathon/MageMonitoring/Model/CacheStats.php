<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

interface Hackathon_MageMonitoring_Model_CacheStats
{
    /**
     * Returns id string, last part of classname in lower case to avoid possible conflicts.
     *
     * @return string
     */
    public function getId();
    /**
     * Returns cache name.
     *
     * @return string
     */
    public function getName();
    /**
     * Returns version string.
     *
     * @return string
     */
    public function getVersion();
    /**
     * Returns true if the cache is currently active.
     *
     * @return bool
     */
    public function isActive();
    /**
     * Returns maximum cache size in bytes or false if not implemented.
     *
     * @return int
     */
    public function getMemoryMax();
    /**
     * Returns used cache size in bytes or false if not implemented.
     *
     * @return int
     */
    public function getMemoryUsed();
    /**
     * Returns cache hit count or false if not implemented.
     *
     * @return int
     */
    public function getCacheHits();
    /**
     * Returns cache miss count or false if not implemented.
     *
     * @return int
     */
    public function getCacheMisses();
    /**
     * Returns array of custom statistics for frontend display or false.
     *
     * Format of return array:
     * array (array ( 'css_class' => 'info|success|warning|error',
     *                 'label' => $label,
     *                 'value' => $value
     *                 'chart' => false|array (see below),
     *        ...
     *        )
     *
     * Format of chart array:
     * array('chart_id' => 'unique_id',
     *         'chart_type' => 'Bar|Doughnut|Line|Pie|PolarArea|Radar',
     *         'canvas_width' => width in pixel as int,
     *         'canvas_height' => height in pixel as int,
     *         'chart_data' => array that matches chart type data structure spec at http://www.chartjs.org/docs/,
     *         'chart_options' => array that matches chart type chart options spec at http://www.chartjs.org/docs/,
     *         )
     *
     * @return false|array
     */
    public function getCustomStats();
    /**
     * Returns init state for collapseable the plugin is displayed in.
     *
     * return bool
     */
    public function displayCollapsed();
    /**
     * Whooosh!
     *
     * @return bool
     */
    public function flushCache();

}
