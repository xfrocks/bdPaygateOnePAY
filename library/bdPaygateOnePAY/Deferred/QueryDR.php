<?php

class bdPaygateOnePAY_Deferred_QueryDR extends XenForo_Deferred_Abstract
{
    public function execute(array $deferred, array $data, $targetRunTime, &$status)
    {
        if (!isset($data['_queryDR_processorClass'])
            || !isset($data['vpc_TransactionNo'])
            || !isset($data['vpc_MerchTxnRef'])
            || !isset($data['vpc_TxnResponseCode'])
        ) {
            XenForo_Error::logException(new Exception(sprintf('Invalid data for %s: %s',
                __METHOD__, XenForo_Helper_Php::safeSerialize($data))));
            return false;
        }

        /** @var bdPaygate_Model_Log $logModel */
        $logModel = XenForo_Model::create('bdPaygate_Model_Log');
        $logs = $logModel->getLogs(array(
            'transaction_id' => sprintf('onepayc_%d', $data['vpc_TransactionNo']),
        ));
        foreach ($logs as $log) {
            if ($log['log_type'] == bdPaygate_Processor_Abstract::PAYMENT_STATUS_ACCEPTED) {
                // this transaction has been accepted, probably via ipn
                return false;
            }
        }

        /** @var bdPaygateOnePAY_Processor_Common $processor */
        $processor = bdPaygateOnePAY_Processor_Common::create($data['_queryDR_processorClass']);
        $result = bdPaygateOnePAY_Helper_Api::queryDR($processor, $data['vpc_MerchTxnRef']);
        if (!is_array($result)
            || !isset($result['vpc_DRExists'])
            || !isset($result['vpc_TxnResponseCode'])
        ) {
            XenForo_Error::logException(new Exception(sprintf('Unexpected query result %s (data=%s)',
                XenForo_Helper_Php::safeSerialize($result), XenForo_Helper_Php::safeSerialize($data))));
            return false;
        }

        if ($result['vpc_DRExists'] !== 'Y'
            || strval($result['vpc_TxnResponseCode']) !== strval($data['vpc_TxnResponseCode'])
        ) {
            XenForo_Error::logException(new Exception(sprintf('Mismatched query result %s (data=%s)',
                XenForo_Helper_Php::safeSerialize($result), XenForo_Helper_Php::safeSerialize($data))));
            return false;
        }

        $params = $data + array('_requestGenerator' => __METHOD__);
        foreach ($result as $resultKey => $resultValue) {
            $params['_queryDR_' . $resultKey] = $resultValue;
        }
        $params['ipn'] = 1;
        $params['vpc_SecureHash'] = $processor->generateSecureHash($params);

        $extraData = array();
        if (!empty($data['itemData']['_extraData'])) {
            $extraData = $data['itemData']['_extraData'];
        }
        $callbackUrl = $processor->bdPaygateOnePay_getCallbackUrl($extraData);

        bdPaygateOnePAY_Helper_Api::pingCallback($callbackUrl, $params);
        return false;
    }
}