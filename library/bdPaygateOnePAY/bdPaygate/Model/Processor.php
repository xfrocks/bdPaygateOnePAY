<?php

class bdPaygateOnePAY_bdPaygate_Model_Processor extends XFCP_bdPaygateOnePAY_bdPaygate_Model_Processor
{
    public function getCurrencies()
    {
        $currencies = parent::getCurrencies();

        $currencies[bdPaygateOnePAY_Processor_Common::CURRENCY_VND] = 'VND';

        return $currencies;
    }

    public function formatAmount($amount, $currency)
    {
        if ($currency === bdPaygateOnePAY_Processor_Common::CURRENCY_VND) {
            return XenForo_Locale::numberFormat($amount);
        }

        return parent::formatAmount($amount, $currency);
    }

    public function truncateAmount($amount, $currency)
    {
        if ($currency === bdPaygateOnePAY_Processor_Common::CURRENCY_VND) {
            return round($amount);
        }

        return parent::truncateAmount($amount, $currency);
    }

    public function getProcessorNames()
    {
        $names = parent::getProcessorNames();

        $names['onepayl'] = 'bdPaygateOnePAY_Processor_Local';
        $names['onepayi'] = 'bdPaygateOnePAY_Processor_International';
        $names['onepay'] = 'bdPaygateOnePAY_Processor_Both';

        return $names;
    }

}
