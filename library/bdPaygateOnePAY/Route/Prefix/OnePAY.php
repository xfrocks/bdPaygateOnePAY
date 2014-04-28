<?php

class bdPaygateOnePAY_Route_Prefix_OnePAY implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		return $router->getRouteMatch('bdPaygateOnePAY_ControllerPublic_OnePAY', $routePath);
	}

}
