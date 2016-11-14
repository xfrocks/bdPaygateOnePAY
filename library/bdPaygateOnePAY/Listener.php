<?php

class bdPaygateOnePAY_Listener
{
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

    public static function load_class_bdPaygate_Model_Processor($class, array &$extend)
    {
        if ($class === 'bdPaygate_Model_Processor') {
            $extend[] = 'bdPaygateOnePAY_bdPaygate_Model_Processor';
        }
    }
}
