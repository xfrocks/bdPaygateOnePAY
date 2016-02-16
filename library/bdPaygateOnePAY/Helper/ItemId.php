<?php

class bdPaygateOnePAY_Helper_ItemId
{
    public static function createUniqueItemId($itemId)
    {
        if (self::pregMatchId($itemId, $matches)) {
            // format matched:  $itemId is an unique id already, return asap
            return $itemId;
        }

        $db = XenForo_Application::getDb();

        $db->insert('xf_onepay_item_id', array(
            'item_id' => $itemId,
            'create_date' => XenForo_Application::$time,
        ));

        $uniqueId = $db->lastInsertId();

        return sprintf('unique/%d', $uniqueId);
    }

    public static function restoreItemIdFromUnique($uniqueItemId)
    {
        if (!self::pregMatchId($uniqueItemId, $matches)) {
            // format not matched: probably some old id, return asap
            return $uniqueItemId;
        }

        $db = XenForo_Application::getDb();

        return $db->fetchOne('
			SELECT item_id
			FROM `xf_onepay_item_id`
			WHERE unique_id = ?
		', $matches['id']);
    }

    public static function cleanUp()
    {
        $db = XenForo_Application::getDb();

        return $db->query('DELETE FROM `xf_onepay_item_id` WHERE create_date < ?',
            XenForo_Application::$time - 90 * 86400);
    }

    protected static function pregMatchId($str, &$matches)
    {
        return preg_match('#^unique/(?<id>\d+)$#', $str, $matches);
    }

}
