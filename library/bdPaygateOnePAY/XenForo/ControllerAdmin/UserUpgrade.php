<?php

class bdPaygateOnePAY_XenForo_ControllerAdmin_UserUpgrade extends XFCP_bdPaygateOnePAY_XenForo_ControllerAdmin_UserUpgrade
{
	public function actionIndex()
	{
		$optionModel = $this->getModelFromCache('XenForo_Model_Option');
		$optionModel->bdPaygateOnePAY_hijackOptions();

		return parent::actionIndex();
	}

}
