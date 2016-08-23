<?php

class bdPaygateOnePAY_Processor_International extends bdPaygateOnePAY_Processor_Common
{
    protected function _getOnePAYId()
    {
        if ($this->_sandboxMode()) {
            /**
             * Test card information:
             * Visa   / 4005550000000001 / 05/17 / 123 (success)
             * Master / 5313581000123430 / 05/17 / 123 (error B, F)
             */
            return 'TESTONEPAY';
        }

        return XenForo_Application::getOptions()->get('bdPaygateOnePAY_zint_id');
    }

    protected function _getOnePAYCode()
    {
        if ($this->_sandboxMode()) {
            return '6BEB2546';
        }

        return XenForo_Application::getOptions()->get('bdPaygateOnePAY_zintcode');
    }

    protected function _getOnePAYHash()
    {
        if ($this->_sandboxMode()) {
            return '6D0870CDE5F24F34F3915FB0045120DB';
        }

        return XenForo_Application::getOptions()->get('bdPaygateOnePAY_zinthash');
    }

    protected function _getOnePAYCallToAction()
    {
        return new XenForo_Phrase('bdpaygateonepay_call_to_action_international');
    }

    protected function _getOnePAYLink()
    {
        return $this->_sandboxMode() ? 'https://mtf.onepay.vn/vpcpay/vpcpay.op' : 'https://onepay.vn/vpcpay/vpcpay.op';
    }

    protected function _getOnePAYUnacceptedMessage()
    {
        $code = '0';
        if (isset($_REQUEST['vpc_TxnResponseCode'])) {
            $code = $_REQUEST['vpc_TxnResponseCode'];
        }

        switch ($code) {
            case '1':
            case '2':
            case '3':
            case '9':
                return new XenForo_Phrase('bdpaygateonepay_message_declined');
            case '4':
                return new XenForo_Phrase('bdpaygateonepay_message_expired');
            case '5':
                return new XenForo_Phrase('bdpaygateonepay_message_insufficient_funds');
            case '6':
            case '7':
            case 'A':
            case 'C':
            case 'D':
            case 'I':
            case 'L':
            case 'N':
            case 'P':
            case 'R':
            case 'S':
            case 'U':
                return new XenForo_Phrase('bdpaygateonepay_message_error');
            case '8':
                return new XenForo_Phrase('bdpaygateonepay_message_no_internet_banking');
            case '99':
                return new XenForo_Phrase('bdpaygateonepay_message_user_cancelled');
            case 'B':
            case 'F':
            case 'W':
                return new XenForo_Phrase('bdpaygateonepay_message_3d_secure');
            case 'E':
                return new XenForo_Phrase('bdpaygateonepay_message_csc_or_declined');
            case 'Z':
                return new XenForo_Phrase('bdpaygateonepay_message_blocked_by_ofd');
        }
    }

    protected function _prepareOnePAYParams(array $params, array $extraData)
    {
        $params = parent::_prepareOnePAYParams($params, $extraData);

        $params['AgainLink'] = $this->_generateReturnUrl($extraData);
        $params['Title'] = XenForo_Application::getOptions()->get('boardTitle');

        return $params;
    }

}
