<?php

class bdPaygateOnePAY_ControllerPublic_OnePAY extends XenForo_ControllerPublic_Abstract
{
	public function actionIndex()
	{
		$input = $this->_input->filter(array(
			'pay' => XenForo_Input::STRING,

			'amount' => XenForo_Input::STRING,
			'currency' => XenForo_Input::STRING,
			'itemName' => XenForo_Input::STRING,
			'itemId' => XenForo_Input::STRING,
			'recurringInterval' => XenForo_Input::STRING,
			'recurringUnit' => XenForo_Input::STRING,
			'extraData' => XenForo_Input::STRING,
		));

		if (empty($input['recurringInterval']) OR empty($input['recurringUnit']))
		{
			$input['recurringInterval'] = false;
			$input['recurringUnit'] = false;
		}
		$input['extraData'] = @json_decode($input['extraData'], true);
		if (empty($input['extraData']))
		{
			$input['extraData'] = array();
		}

		$local = bdPaygate_Processor_Abstract::create('bdPaygateOnePAY_Processor_Local');
		$international = bdPaygate_Processor_Abstract::create('bdPaygateOnePAY_Processor_International');

		switch ($input['pay'])
		{
			case 'local':
				$link = call_user_func_array(array(
					$local,
					'bdPaygateOnePAY_getLink',
				), array(
					$input['amount'],
					$input['currency'],
					$input['itemName'],
					$input['itemId'],
					$input['recurringInterval'],
					$input['recurringUnit'],
					$input['extraData'],
				));
				$returnUrl = $local->bdPaygateOnePAY_getReturnUrl($input['extraData']);
				break;
			case 'international':
				$link = call_user_func_array(array(
					$international,
					'bdPaygateOnePAY_getLink',
				), array(
					$input['amount'],
					$input['currency'],
					$input['itemName'],
					$input['itemId'],
					$input['recurringInterval'],
					$input['recurringUnit'],
					$input['extraData'],
				));
				$returnUrl = $international->bdPaygateOnePAY_getReturnUrl($input['extraData']);
				break;
		}

		if (!empty($link))
		{
			if (!empty($returnUrl))
			{
				XenForo_Application::getSession()->set('_bdPaygateOnePAY_returnUrl', $returnUrl);
			}

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $link);
		}

		$viewParams = $input;
		$viewParams['localAvailable'] = $local->isAvailable();
		$viewParams['internationalAvailable'] = $international->isAvailable();
		$viewParams['bothAvailable'] = ($viewParams['localAvailable'] AND $viewParams['internationalAvailable']);

		return $this->responseView('bdPaygateOnePAY_ViewPublic_OnePAY_Index', 'bdpaygateonepay_onepay_index', $viewParams);
	}

	public function actionComplete()
	{
		$paymentAccepted = $this->_input->filterSingle('payment_accepted', XenForo_Input::STRING);
		$message = $this->_input->filterSingle('message', XenForo_Input::STRING);
		$returnUrl = $this->_input->filterSingle('return_url', XenForo_Input::STRING);

		$viewParams = array(
			'paymentAccepted' => $paymentAccepted,
			'message' => $message,
			'returnUrl' => $returnUrl,
		);

		return $this->responseView('bdPaygateOnePAY_ViewPublic_OnePAY_Complete', 'bdpaygateonepay_onepay_complete', $viewParams);
	}

}
