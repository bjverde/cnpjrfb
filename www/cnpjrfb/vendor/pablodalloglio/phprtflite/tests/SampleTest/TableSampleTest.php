<?php

/**
 * Created on 08.04.2010
 *
 * @author sz
 */
class TableSampleTest extends PHPRtfLiteSampleTestCase
{

    private $_name = 'tables';

    public function test()
    {
        $this->processTest($this->_name . '.php');
    }

    protected function getSampleFile()
    {
        return $this->getSampleDir() . '/generated/' . $this->_name . '.rtf';
    }

}
