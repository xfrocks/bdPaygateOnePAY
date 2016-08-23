<?php

class bdPaygateOnePAY_Processor_Local extends bdPaygateOnePAY_Processor_Common
{
    protected function _getOnePAYId()
    {
        if ($this->_sandboxMode()) {
            /**
             * Test card information:
             * Vietcombank / 6868682607535021 / 12/08 / NGUYEN HONG NHUNG
             */
            return 'ONEPAY';
        }

        return XenForo_Application::getOptions()->get('bdPaygateOnePAY__id');
    }

    protected function _getOnePAYCode()
    {
        if ($this->_sandboxMode()) {
            return 'D67342C2';
        }

        return XenForo_Application::getOptions()->get('bdPaygateOnePAY_code');
    }

    protected function _getOnePAYHash()
    {
        if ($this->_sandboxMode()) {
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

    protected function _getOnePAYUnacceptedMessage()
    {
        $code = '0';
        if (isset($_REQUEST['vpc_TxnResponseCode'])) {
            $code = $_REQUEST['vpc_TxnResponseCode'];
        }

        switch ($code) {
            case '1':
                return new XenForo_Phrase('bdpaygateonepay_message_declined');
            case '3':
            case '4':
            case '5':
            case '6':
                // internal errors, do not show to user
                return '';
            case '7':
                return new XenForo_Phrase('bdpaygateonepay_message_error');
            case '8':
                return new XenForo_Phrase('bdpaygateonepay_message_invalid_number');
            case '9':
                return new XenForo_Phrase('bdpaygateonepay_message_invalid_name');
            case '10':
                return new XenForo_Phrase('bdpaygateonepay_message_expired');
            case '11':
                return new XenForo_Phrase('bdpaygateonepay_message_no_internet_banking');
            case '12':
                return new XenForo_Phrase('bdpaygateonepay_message_invalid_date');
            case '13':
            case '21':
                return new XenForo_Phrase('bdpaygateonepay_message_insufficient_funds');
            case '99':
                return new XenForo_Phrase('bdpaygateonepay_message_user_cancelled');
        }
    }

    protected function _prepareOnePAYParams(array $params, array $extraData)
    {
        $params = parent::_prepareOnePAYParams($params, $extraData);

        $params['vpc_Currency'] = 'VND';

        return $params;
    }

}
