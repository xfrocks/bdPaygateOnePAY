<?php

class bdPaygateOnePAY_XenForo_Model_UserUpgrade extends XFCP_bdPaygateOnePAY_XenForo_MOdel_UserUpgrade
{
	public function prepareUserUpgrade(array $upgrade)
	{
		$upgrade = parent::prepareUserUpgrade($upgrade);

		if ($upgrade['cost_currency'] === bdPaygateOnePAY_Processor_Common::CURRENCY_VND)
		{
			$cost = XenForo_Template_Helper_Core::numberFormat($upgrade['cost_amount'], 0);
			$cost .= ' VND';

			if ($upgrade['costPhrase'] instanceof XenForo_Phrase)
			{
				$upgrade['costPhrase']->setParams(array('cost' => $cost));
			}
			else
			{
				$upgrade['costPhrase'] = $cost;
			}
		}

		return $upgrade;
	}

}
