<?php

class bdPaygateOnePAY_XenForo_Model_Option extends XFCP_bdPaygateOnePAY_XenForo_Model_Option
{
	// this property must be static because
	// XenForo_ControllerAdmin_UserUpgrade::actionIndex
	// for no apparent reason use XenForo_Model::create to create the optionModel
	// (instead of using XenForo_Controller::getModelFromCache)
	private static $_bdPaygateOnePAY_hijackOptions = false;

	public function getOptionsByIds(array $optionIds, array $fetchOptions = array())
	{
		if (self::$_bdPaygateOnePAY_hijackOptions === true)
		{
			$optionIds[] = 'bdPaygateOnePAY__id';
			$optionIds[] = 'bdPaygateOnePAY_code';
			$optionIds[] = 'bdPaygateOnePAY_hash';
		}

		$options = parent::getOptionsByIds($optionIds, $fetchOptions);

		self::$_bdPaygateOnePAY_hijackOptions = false;

		return $options;
	}

	public function bdPaygateOnePAY_hijackOptions()
	{
		self::$_bdPaygateOnePAY_hijackOptions = true;
	}

}
