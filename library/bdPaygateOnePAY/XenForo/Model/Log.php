<?php

class bdPaygateOnePAY_XenForo_Model_Log extends XFCP_bdPaygateOnePAY_XenForo_Model_Log
{
    public function pruneAdminLogEntries($pruneDate = null)
    {
        bdPaygateOnePAY_Helper_ItemId::cleanUp();

        parent::pruneAdminLogEntries($pruneDate);
    }

}
