<?php

class bdPaygateOnePAY_XenForo_ControllerAdmin_Log extends XFCP_bdPaygateOnePAY_XenForo_ControllerAdmin_Log
{
    public function actionBdpaygate()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $response = parent::actionBdpaygate();

        if ($response instanceof XenForo_ControllerResponse_View
            && !empty($response->params['entry'])
        ) {
            $logEntry = $response->params['entry'];
            $processorClass = null;
            switch ($logEntry['processor']) {
                case 'onepayi':
                    $processorClass = 'bdPaygateOnePAY_Processor_International';
                    break;
                case 'onepayl':
                    $processorClass = 'bdPaygateOnePAY_Processor_Local';
                    break;
            }

            if (!empty($processorClass)
                && $this->_input->filterSingle('queryDr', XenForo_Input::BOOLEAN)
            ) {
                XenForo_Application::defer('bdPaygateOnePAY_Deferred_QueryDR', $logEntry['logDetails']
                    + array('_queryDR_processorClass' => $processorClass));
            }
        }

        return $response;
    }
}