<?php

class bdPaygateOnePAY_bdShop_Helper_Stock_Pricings extends XFCP_bdPaygateOnePAY_bdShop_Helper_Stock_Pricings
{
	protected function _prepare(array $stock, $comment, $data, XenForo_View $view)
	{
		$prepared = parent::_prepare($stock, $comment, $data, $view);

		foreach ($prepared['rendered'] as &$rendered)
		{
			if (utf8_strtolower($rendered['currency']) === bdPaygateOnePAY_Processor_Common::CURRENCY_VND)
			{
				$rendered['amount'] = XenForo_Template_Helper_Core::numberFormat($rendered['input']['amount'], 0);
			}
		}

		return $prepared;
	}

}
