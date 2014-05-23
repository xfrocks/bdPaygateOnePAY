<?php

class bdPaygateOnePAY_Listener
{
	public static function load_class($class, array &$extend)
	{
		static $classes = array(
			'bdPaygate_Model_Processor',

			'XenForo_ControllerAdmin_UserUpgrade',
			'XenForo_Model_Log',
			'XenForo_Model_Option',
		);

		if (in_array($class, $classes))
		{
			$extend[] = 'bdPaygateOnePAY_' . $class;
		}
	}

	public static function load_class_XenForo_Model_UserUpgrade($class, array &$extend)
	{
		if ($class === 'XenForo_Model_UserUpgrade')
		{
			$extend[] = 'bdPaygateOnePAY_XenForo_Model_UserUpgrade';
		}
	}

	public static function load_class_bdShop_Helper_Stock_Pricings($class, array &$extend)
	{
		if ($class === 'bdShop_Helper_Stock_Pricings')
		{
			$extend[] = 'bdPaygateOnePAY_bdShop_Helper_Stock_Pricings';
		}
	}

	public static function load_class_model_bdShop_Model_Stock($class, array &$extend)
	{
		if ($class === 'bdShop_Model_Stock')
		{
			$extend[] = 'bdPaygateOnePAY_bdShop_Model_Stock';
		}
	}

	public static function file_health_check(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
	{
		$hashes += bdPaygateOnePAY_FileSums::getHashes();
	}

}
