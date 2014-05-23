<?php

class bdPaygateOnePAY_Processor_Both extends bdPaygateOnePAY_Processor_Common
{
	protected function _getOnePAYId()
	{
		if ($this->_sandboxMode())
		{
			return 'always';
		}

		$options = XenForo_Application::getOptions();

		$localId = $options->get('bdPaygateOnePAY__id');
		if (!empty($localId))
		{
			return 'local';
		}

		$internationalId = $options->get('bdPaygateOnePAY_zint_id');
		if (!empty($internationalId))
		{
			return 'international';
		}

		return '';
	}

	protected function _getOnePAYCode()
	{
		return 'N/A';
	}

	protected function _getOnePAYHash()
	{
		return 'N/A';
	}

	protected function _getOnePAYCallToAction()
	{
		return new XenForo_Phrase('bdpaygateonepay_call_to_action_both');
	}

	protected function _getOnePAYLink()
	{
		return '';
	}

	public function generateFormData($amount, $currency, $itemName, $itemId, $recurringInterval = false, $recurringUnit = false, array $extraData = array())
	{
		$form = sprintf('<a href="%1$s" class="button OverlayTrigger">%2$s</a>', XenForo_Link::buildPublicLink('onepay', '', array(
			'amount' => $amount,
			'currency' => $currency,
			'itemName' => $itemName,
			'itemId' => $itemId,
			'recurringInterval' => $recurringInterval,
			'recurringUnit' => $recurringUnit,
			'extraData' => XenForo_Template_Helper_Core::callHelper('json', array($extraData)),
		)), $this->_getOnePAYCallToAction());

		return $form;
	}

}
