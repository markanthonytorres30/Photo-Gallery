<?php


interface XRL_EncoderInterface
{
    public function encodeRequest(XRL_Request $request);
    public function encodeError(Exception $error);
    public function encodeResponse($response);
}

