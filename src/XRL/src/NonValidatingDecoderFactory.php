<?php


class       XRL_NonValidatingDecoderFactory
implements  XRL_DecoderFactoryInterface
{
    public function createDecoder()
    {
        return new XRL_Decoder(FALSE);
    }
}

