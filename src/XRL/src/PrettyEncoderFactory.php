<?php


class       XRL_PrettyEncoderFactory
implements  XRL_EncoderFactoryInterface
{
    public function createEncoder()
    {
        return new XRL_Encoder(XRL_Encoder::OUTPUT_PRETTY);
    }
}

