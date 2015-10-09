<?php

class       XRL_ValidatingDecoderFactory
implements  XRL_DecoderFactoryInterface
{
    public function createDecoder()
    {
        return new XRL_Decoder(TRUE);
    }
}

