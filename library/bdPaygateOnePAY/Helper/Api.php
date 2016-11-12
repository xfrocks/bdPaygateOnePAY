<?php

class bdPaygateOnePAY_Helper_Api
{
    public static function queryDR(bdPaygateOnePAY_Processor_Common $processor, $merchTxnRef)
    {
        $params = array(
            'vpc_Command' => 'queryDR',
            'vpc_Version' => '1',
            'vpc_MerchTxnRef' => $merchTxnRef,
            'vpc_Merchant' => $processor->_getOnePAYId(),
            'vpc_AccessCode' => $processor->_getOnePAYCode(),
            'vpc_User' => 'op01',
            'vpc_Password' => 'op123456',
        );

        $uri = $processor->getQueryLink();
        $client = XenForo_Helper_Http::getClient($uri);
        $client->setParameterGet($params);

        $responseStatus = 500;
        $responseBody = '';
        $requestException = null;
        try {
            $response = $client->request('POST');
            $responseStatus = $response->getStatus();
            $responseBody = $response->getBody();
        } catch (Zend_Http_Client_Exception $e) {
            $requestException = $e;
        }

        $result = array();
        if ($responseStatus === 200) {
            parse_str($responseBody, $result);
        }

        if ($requestException !== null) {
            XenForo_Error::logException($requestException);
        }

        if (XenForo_Application::debugMode()) {
            $paramsStr = '';
            foreach ($params as $paramKey => $paramValue) {
                $paramsStr .= sprintf('&%s=%s', $paramKey, $paramValue);
            }

            XenForo_Helper_File::log(__CLASS__, sprintf('queryDR(%1$s, %2$s):' . "\n"
                . '    request=(%3$s, %4$s)' . "\n"
                . '    response=(%5$d, %6$s)',
                get_class($processor), $merchTxnRef,
                $uri, $paramsStr,
                $responseStatus, $responseBody
            ));
        }

        return $result;
    }

    public static function pingCallback($callbackUrl, array $params)
    {
        $client = XenForo_Helper_Http::getClient($callbackUrl);
        $client->setParameterGet($params);

        $responseStatus = 500;
        $responseBody = '';
        $requestException = null;
        try {
            $response = $client->request('GET');
            $responseStatus = $response->getStatus();
            $responseBody = $response->getBody();
        } catch (Zend_Http_Client_Exception $e) {
            $requestException = $e;
        }

        if ($requestException !== null) {
            XenForo_Error::logException($requestException);
        }

        if (XenForo_Application::debugMode()) {
            $paramsStr = '';
            foreach ($params as $paramKey => $paramValue) {
                $paramsStr .= sprintf('&%s=%s', $paramKey, $paramValue);
            }

            XenForo_Helper_File::log(__CLASS__, sprintf('pingCallback:' . "\n"
                . '    request=(%1$s, %2$s)' . "\n"
                . '    response=(%3$d, %4$s)',
                $callbackUrl, $paramsStr,
                $responseStatus, $responseBody
            ));
        }
    }
}