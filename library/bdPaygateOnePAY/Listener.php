<?php

class bdPaygateOnePAY_Listener
{
    public static function load_class($class, array &$extend)
    {
        static $classes = array(
            'bdPaygate_Model_Processor',
            'XenForo_Model_Log',
        );

        if (in_array($class, $classes)) {
            $extend[] = 'bdPaygateOnePAY_' . $class;
        }
    }

    public static function file_health_check(
        /** @noinspection PhpUnusedParameterInspection */
        XenForo_ControllerAdmin_Abstract $controller,
        array &$hashes
    ) {
        $hashes += bdPaygateOnePAY_FileSums::getHashes();
    }


    public static function load_class_XenForo_ControllerAdmin_Log($class, array &$extend)
    {
        if ($class === 'XenForo_ControllerAdmin_Log') {
            $extend[] = 'bdPaygateOnePAY_XenForo_ControllerAdmin_Log';
        }
    }
}
