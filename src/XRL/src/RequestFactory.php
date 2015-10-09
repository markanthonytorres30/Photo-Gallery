<?php


class       XRL_RequestFactory
implements  XRL_RequestFactoryInterface
{
    public function createRequest($method, array $params)
    {
        return new XRL_Request($method, $params);
    }
}

