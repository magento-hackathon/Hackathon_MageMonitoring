<?php

class Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Chart
    extends Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract
    implements Hackathon_MageMonitoring_Block_Widget_Multi_Renderer
{
    const CONTENT_TYPE_CHART = 'chart';

    /**
     * Retrieve the data for the block output.
     *
     * @return mixed
     */
    public function _getContent()
    {
        return $this->getValues();
    }

    /**
     * Add new slice to pie chart.
     *
     * @param $title
     * @param $value
     * @return $this
     */
    public function addValue($title, $value)
    {
        if (is_null($this->getValues())) {
            $this->setValues(array());
        }

        $values = $this->getValues();
        $values[$title] = $value;
        $this->setValues($values);

        return $this;
    }

}
