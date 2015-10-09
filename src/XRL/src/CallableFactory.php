<?php


class       XRL_CallableFactory
implements  XRL_CallableFactoryInterface
{
    public function fromPHP($callable)
    {
        return new XRL_Callable($callable);
    }
}

