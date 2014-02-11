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

interface Hackathon_MageMonitoring_Model_Widget_CacheStat extends Hackathon_MageMonitoring_Model_Widget
{
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
     * Whooosh!
     *
     * @return bool
     */
    public function flushCache();

}
