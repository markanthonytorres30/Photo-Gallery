<?php


interface XRL_RequestInterface
{
    public function __construct($procedure, array $params);

    public function getProcedure();

    public function getParams();
}
