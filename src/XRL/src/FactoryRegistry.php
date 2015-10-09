<?php


abstract class  XRL_FactoryRegistry
implements      ArrayAccess
{
    protected $_interfaces;

    public function offsetSet($interface, $obj)
    {
        if (!is_string($interface))
            ; /// @TODO

        if (!is_object($obj))
            ; /// @TODO

        $interface = strtolower($interface);
        if (!isset($this->_interfaces[$interface]))
            ; /// @TODO

        if (!($obj instanceof $interface))
            ; /// @TODO

        $this->_interfaces[$interface] = $obj;
    }

    public function offsetGet($interface)
    {
        if (!is_string($interface))
            ; /// @TODO

        $interface = strtolower($interface);
        return $this->_interfaces[$interface];
    }

    public function offsetExists($interface)
    {
        if (!is_string($interface))
            return FALSE;

        $interface = strtolower($interface);
        return isset($this->_interfaces[$interface]);
    }

    public function offsetUnset($interface)
    {
        /// @TODO
    }
}

