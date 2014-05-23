<?php

class bdPaygateOnePAY_bdShop_Model_Stock extends XFCP_bdPaygateOnePAY_bdShop_Model_Stock
{
	public function prepareStock(array $stock, array $viewingUser = null)
	{
		$stock = parent::prepareStock($stock, $viewingUser);

		foreach ($stock['stock_pricing'] as $pricing)
		{
			if ($pricing['currency'] === bdPaygateOnePAY_Processor_Common::CURRENCY_VND)
			{
				$stock['stockPricing'][$pricing['currency']] = sprintf('%s %s', utf8_strtoupper($pricing['currency']), XenForo_Template_Helper_Core::numberFormat($pricing['amount'], 0));
			}
		}

		return $stock;
	}

}
