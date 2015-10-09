<?php


class       XRL_ResponseFactory
implements  XRL_ResponseFactoryInterface
{
    public function createResponse($response)
    {
        return new XRL_Response($response);
    }
}

