<?php

abstract class bdPaygateOnePAY_Processor_Common extends bdPaygate_Processor_Abstract
{
	const CURRENCY_VND = 'vnd';

	abstract protected function _getOnePAYId();
	abstract protected function _getOnePAYCode();
	abstract protected function _getOnePAYHash();
	abstract protected function _getOnePAYCallToAction();
	abstract protected function _getOnePAYLink();

	public function getSupportedCurrencies()
	{
		return array(bdPaygateOnePAY_Processor_Common::CURRENCY_VND);
	}

	public function isRecurringSupported()
	{
		return false;
	}

	public function validateCallback(Zend_Controller_Request_Http $request, &$transactionId, &$paymentStatus, &$transactionDetails, &$itemId)
	{
		$amount = false;
		$currency = false;

		return $this->validateCallback2($request, $transactionId, $paymentStatus, $transactionDetails, $itemId, $amount, $currency);
	}

	public function validateCallback2(Zend_Controller_Request_Http $request, &$transactionId, &$paymentStatus, &$transactionDetails, &$itemId, &$amount, &$currency)
	{
		$input = new XenForo_Input($request);

		$filtered = $input->filter(array(
			'vpc_MerchTxnRef' => XenForo_Input::STRING,
			'vpc_Merchant' => XenForo_Input::STRING,
			'vpc_OrderInfo' => XenForo_Input::STRING,
			'vpc_Amount' => XenForo_Input::UINT,
			'vpc_TxnResponseCode' => XenForo_Input::STRING,
			'vpc_TransactionNo' => XenForo_Input::STRING,
			'vpc_SecureHash' => XenForo_Input::STRING,
		));

		$params = $_REQUEST;
		if (isset($params['vpc_SecureHash']))
		{
			unset($params['vpc_SecureHash']);
		}
		$secureHash = $this->_generateSecureHash($params);
		if ($secureHash !== $filtered['vpc_SecureHash'])
		{
			$this->_setError('Request not validated');
			return false;
		}

		$transactionId = (!empty($filtered['vpc_TransactionNo']) ? ('onepayc_' . $filtered['vpc_TransactionNo']) : '');
		$transactionDetails = array_merge($_REQUEST, $filtered);
		$itemId = $this->_parseMerchTxnRef($filtered['vpc_MerchTxnRef']);
		$amount = $filtered['vpc_Amount'] / 100;
		$currency = bdPaygateOnePAY_Processor_Common::CURRENCY_VND;
		$processorModel = $this->getModelFromCache('bdPaygate_Model_Processor');

		if ($filtered['vpc_TxnResponseCode'] === '0')
		{
			$paymentStatus = bdPaygate_Processor_Abstract::PAYMENT_STATUS_ACCEPTED;
		}
		else
		{
			$paymentStatus = bdPaygate_Processor_Abstract::PAYMENT_STATUS_REJECTED;
		}

		return true;
	}

	public function generateFormData($amount, $currency, $itemName, $itemId, $recurringInterval = false, $recurringUnit = false, array $extraData = array())
	{
		$this->_assertAmount($amount);
		$this->_assertCurrency($currency);
		$this->_assertItem($itemName, $itemId);
		$this->_assertRecurring($recurringInterval, $recurringUnit);

		$params = array(
			'vpc_Version' => 2,
			'vpc_Command' => 'pay',
			'vpc_Merchant' => $this->_getOnePAYId(),
			'vpc_AccessCode' => $this->_getOnePAYCode(),
			'vpc_MerchTxnRef' => $this->_generateMerchTxnRef($itemId),
			'vpc_OrderInfo' => $itemName,
			'vpc_Amount' => floor($amount * 100),
			'vpc_Locale' => $this->_getLocale(),
			'vpc_TicketNo' => $this->_getTicketNo(),
			'vpc_ReturnURL' => $this->_generateCallbackUrl($extraData),
		);

		$params = $this->_prepareOnePAYParams($params, $extraData);

		$params['vpc_SecureHash'] = $this->_generateSecureHash($params);

		$callToAction = $this->_getOnePAYCallToAction();
		$link = $this->_getOnePAYLink();

		return $this->_generateOnePAYForm($link, $callToAction, $params, $extraData);
	}

	public function redirectOnCallback(Zend_Controller_Request_Http $request, $paymentStatus, $processMessage)
	{
		if (!empty($_REQUEST['ipn']))
		{
			if ($paymentStatus == bdPaygate_Processor_Abstract::PAYMENT_STATUS_ACCEPTED)
			{
				die('responsecode=1&desc=confirm-success');
			}
			else
			{
				die('responsecode=0&desc=confirm-fail');
			}
		}

		$message = '';
		if ($paymentStatus == bdPaygate_Processor_Abstract::PAYMENT_STATUS_REJECTED)
		{
			if (!empty($_REQUEST['vpc_Message']))
			{
				$message = $_REQUEST['vpc_Message'];
			}
		}

		if (XenForo_Application::isRegistered('session'))
		{
			$session = XenForo_Application::getSession();
		}
		else
		{
			$session = XenForo_Session::startPublicSession($request);
		}

		$returnUrl = $session->get('_bdPaygateOnePAY_returnUrl');

		header('Location: ' . XenForo_Link::buildPublicLink('canonical:onepay/complete', '', array(
			'payment_accepted' => $paymentStatus == bdPaygate_Processor_Abstract::PAYMENT_STATUS_ACCEPTED ? 1 : 0,
			'message' => $message,
			'return_url' => $returnUrl,
		)));
		die();
	}

	protected function _generateOnePAYForm($link, $callToAction, $params, $extraData)
	{
		$i = 0;
		foreach ($params as $paramKey => $paramValue)
		{
			$link .= ($i == 0 ? '?' : '&');
			$link .= sprintf('%s=%s', urlencode($paramKey), urlencode($paramValue));

			$i++;
		}

		$form = sprintf('<a href="%1$s" class="button OverlayTrigger">%2$s</a>', XenForo_Link::buildPublicLink('onepay', '', array(
			'local' => $this instanceof bdPaygateOnePAY_Processor_Local ? 1 : 0,
			'redirect' => $link,
			'return_url' => $this->_generateReturnUrl($extraData),
		)), $callToAction);

		return $form;
	}

	protected function _generateCallbackUrl($extraData)
	{
		if ($this instanceof bdPaygateOnePAY_Processor_Local)
		{
			return XenForo_Application::getOptions()->get('boardUrl') . '/bdpaygate/onepayl.php';
		}
		else
		{
			return XenForo_Application::getOptions()->get('boardUrl') . '/bdpaygate/onepayi.php';
		}
	}

	protected function _generateMerchTxnRef($itemId)
	{
		return sprintf('%d/%s', XenForo_Application::$time, $itemId);
	}

	protected function _generateSecureHash(array &$params)
	{
		$data = '';
		$hash = $this->_getOnePAYHash();

		$paramKeys = array_keys($params);
		sort($paramKeys);

		foreach ($paramKeys as $paramKey)
		{
			$params[$paramKey] = strval($params[$paramKey]);
			$paramValue = $params[$paramKey];

			if (strlen($paramValue) == 0)
			{
				unset($params[$paramKey]);
				continue;
			}

			if ((substr($paramKey, 0, 4) === 'vpc_') || (substr($paramKey, 0, 5) === 'user_'))
			{
				$data .= sprintf('%s=%s&', $paramKey, $paramValue);
			}
		}

		$data = rtrim($data, '&');

		return strtoupper(hash_hmac('SHA256', $data, pack('H*', $hash)));
	}

	protected function _getLocale()
	{
		return 'vn';
	}

	protected function _getTicketNo()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	}

	protected function _parseMerchTxnRef($merchTxnRef)
	{
		if (preg_match('#^[0-9]+/#', $merchTxnRef, $matches))
		{
			return substr($merchTxnRef, strlen($matches[0]));
		}

		return false;
	}

	protected function _prepareOnePAYParams(array $params, array $extraData)
	{
		return $params;
	}

}
