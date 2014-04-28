<?php

class bdPaygateOnePAY_ControllerPublic_OnePAY extends XenForo_ControllerPublic_Abstract
{
	public function actionIndex()
	{
		$redirect = $this->_input->filterSingle('redirect', XenForo_Input::STRING);
		if (empty($redirect))
		{
			return $this->responseNoPermission();
		}

		$parsedUrl = parse_url($redirect);
		if (empty($parsedUrl['host']))
		{
			return $this->responseNoPermission();
		}
		if (!in_array($parsedUrl['host'], array(
			'mtf.onepay.vn',
			'onepay.vn'
		)))
		{
			return $this->responseNoPermission();
		}

		$returnUrl = $this->_input->filterSingle('return_url', XenForo_Input::STRING);
		XenForo_Application::getSession()->set('_bdPaygateOnePAY_returnUrl', $returnUrl);

		$viewParams = array('redirect' => $redirect);

		return $this->responseView('bdPaygateOnePAY_ViewPublic_OnePAY_Index', 'bdpaygateonepay_onepay_index', $viewParams);
	}

}
