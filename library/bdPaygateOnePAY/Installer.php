<?php

class bdPaygateOnePAY_Installer
{
	/* Start auto-generated lines of code. Change made will be overwriten... */

	protected static $_tables = array();
	protected static $_patches = array();

	public static function install($existingAddOn, $addOnData)
	{
		$db = XenForo_Application::get('db');

		foreach (self::$_tables as $table)
		{
			$db->query($table['createQuery']);
		}

		foreach (self::$_patches as $patch)
		{
			$tableExisted = $db->fetchOne($patch['showTablesQuery']);
			if (empty($tableExisted))
			{
				continue;
			}

			$existed = $db->fetchOne($patch['showColumnsQuery']);
			if (empty($existed))
			{
				$db->query($patch['alterTableAddColumnQuery']);
			}
		}

		self::installCustomized($existingAddOn, $addOnData);
	}

	public static function uninstall()
	{
		$db = XenForo_Application::get('db');

		foreach (self::$_patches as $patch)
		{
			$tableExisted = $db->fetchOne($patch['showTablesQuery']);
			if (empty($tableExisted))
			{
				continue;
			}

			$existed = $db->fetchOne($patch['showColumnsQuery']);
			if (!empty($existed))
			{
				$db->query($patch['alterTableDropColumnQuery']);
			}
		}

		foreach (self::$_tables as $table)
		{
			$db->query($table['dropQuery']);
		}

		self::uninstallCustomized();
	}

	/* End auto-generated lines of code. Feel free to make changes below */

	public static function installCustomized($existingAddOn, $addOnData)
	{
		$db = XenForo_Application::getDb();

		$db->query('
			CREATE TABLE IF NOT EXISTS `xf_onepay_item_id` (
				`unique_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`item_id` TEXT,
				`create_date` int(10) unsigned NOT NULL,
				PRIMARY KEY (`unique_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		');
	}

	public static function uninstallCustomized()
	{
		// customized uninstall script goes here
	}

}
