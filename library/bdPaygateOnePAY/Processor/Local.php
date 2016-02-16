<?php

class bdPaygateOnePAY_Processor_Local extends bdPaygateOnePAY_Processor_Common
{
	protected function _getOnePAYId()
	{
		if ($this->_sandboxMode())
		{
			return 'ONEPAY';
		}

		return XenForo_Application::getOptions()->get('bdPaygateOnePAY__id');
	}

	protected function _getOnePAYCode()
	{
		if ($this->_sandboxMode())
		{
			return 'D67342C2';
		}

		return XenForo_Application::getOptions()->get('bdPaygateOnePAY_code');
	}

	protected function _getOnePAYHash()
	{
		if ($this->_sandboxMode())
		{
			return 'A3EFDFABA8653DF2342E8DAC29B51AF0';
		}

		return XenForo_Application::getOptions()->get('bdPaygateOnePAY_hash');
	}

	protected function _getOnePAYCallToAction()
	{
		return new XenForo_Phrase('bdpaygateonepay_call_to_action_local');
	}

	protected function _getOnePAYLink()
	{
		return $this->_sandboxMode() ? 'https://mtf.onepay.vn/onecomm-pay/vpc.op' : 'https://onepay.vn/onecomm-pay/vpc.op';
	}

	protected function _prepareOnePAYParams(array $params, array $extraData)
	{
		$params = parent::_prepareOnePAYParams($params, $extraData);

		$params['vpc_Currency'] = 'VND';

		return $params;
	}

}
