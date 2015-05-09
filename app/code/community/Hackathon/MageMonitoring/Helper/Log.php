<?php

class Hackathon_MageMonitoring_Helper_Log
    extends Mage_Core_Helper_Abstract
{

    const CONFIG_LOGGING = 'magemonitoring/general/logging';
    const CONFIG_DEBUG_LOGGING = 'magemonitoring/general/debug_logging';

    /**
     * Returns whether logging is generally activated.
     *
     * @param null|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isLoggingActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_LOGGING, $store);
    }

    /**
     * Returns whether debug logging is activated.
     *
     * @param null|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isDebugLoggingActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_DEBUG_LOGGING, $store);
    }

    /**
     * Logs a message to a file called "NAMESPACE_MODULE.log".
     *
     * A log level can be specified. If the log level is higher than Zend_Log::NOTICE, the logging depends
     * on the activation of verbose logging,
     *
     * @param string $msg
     * @param null|int $level see Zend_Log for constants
     * @return $this
     */
    public function log($msg, $level = null)
    {
        /** @var $configHelper Hackathon_MageMonitoring_Helper_Config */
        $configHelper = Mage::helper('magemonitoring/config');

        if (!$configHelper->isModuleActive() || !$this->isLoggingActive()) {
            return $this;
        }

        // if debug logging is disabled, log only errors
        if ($this->isDebugLoggingActive() || (!is_null($level) && $level < Zend_Log::INFO)) {
            Mage::log($msg, $level, $this->_getModuleName() . '.log');
        }

        return $this;
    }

    /**
     * @param Exception $exception
     * @param null|string $msg
     * @return $this
     */
    public function logException($exception, $msg = null)
    {
        /** @var $configHelper Hackathon_MageMonitoring_Helper_Config */
        $configHelper = Mage::helper('magemonitoring/config');

        if (!$configHelper->isModuleActive() || !$this->isLoggingActive()) {
            return $this;
        }

        // optional message to log
        if (!empty($msg)) {
            $this->log($msg);
        }

        // log exception with backtrace
        Mage::logException($exception);
        $this->log($exception->__toString(), Zend_Log::ERR);

        return $this;
    }

}
