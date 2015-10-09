<?php


interface XRL_RequestFactoryInterface
{
    public function createRequest($method, array $params);
}

