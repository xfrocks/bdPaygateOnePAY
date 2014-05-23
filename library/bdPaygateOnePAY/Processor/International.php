<?php

class bdPaygateOnePAY_Processor_International extends bdPaygateOnePAY_Processor_Common
{
	protected function _getOnePAYId()
	{
		if ($this->_sandboxMode())
		{
			return 'TESTONEPAY';
		}

		return XenForo_Application::getOptions()->get('bdPaygateOnePAY_zint_id');
	}

	protected function _getOnePAYCode()
	{
		if ($this->_sandboxMode())
		{
			return '6BEB2546';
		}

		return XenForo_Application::getOptions()->get('bdPaygateOnePAY_zintcode');
	}

	protected function _getOnePAYHash()
	{
		if ($this->_sandboxMode())
		{
			return '6D0870CDE5F24F34F3915FB0045120DB';
		}

		return XenForo_Application::getOptions()->get('bdPaygateOnePAY_zinthash');
	}

	protected function _getOnePAYCallToAction()
	{
		return new XenForo_Phrase('bdpaygateonepay_call_to_action_international');
	}

	protected function _getOnePAYLink()
	{
		return $this->_sandboxMode() ? 'http://mtf.onepay.vn/vpcpay/vpcpay.op' : 'https://onepay.vn/vpcpay/vpcpay.op';
	}

	protected function _prepareOnePAYParams(array $params, array $extraData)
	{
		$params = parent::_prepareOnePAYParams($params, $extraData);

		$params['AgainLink'] = $this->_generateReturnUrl($extraData);
		$params['Title'] = XenForo_Application::getOptions()->get('boardTitle');

		return $params;
	}

}
