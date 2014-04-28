<?php

class bdPaygateOnePAY_bdPaygate_Model_Processor extends XFCP_bdPaygateOnePAY_bdPaygate_Model_Processor
{
	public function getCurrencies()
	{
		$currencies = parent::getCurrencies();

		$currencies[bdPaygateOnePAY_Processor_Common::CURRENCY_VND] = 'VND';

		return $currencies;
	}

	public function getProcessorNames()
	{
		$names = parent::getProcessorNames();

		$names['onepayl'] = 'bdPaygateOnePAY_Processor_Local';
		$names['onepayi'] = 'bdPaygateOnePAY_Processor_International';

		return $names;
	}

}
