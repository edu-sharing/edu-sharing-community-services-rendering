<?php

/**
 *
 *
 *
 */
abstract class ESRender_Exception_Abstract
extends Exception
{
	public function __construct($Message = '', $Code = '', $Previous = null)
	{
	    if(!empty($Message))
            $this -> message = $Message;
        if(!empty($Code))
            $this -> code = $Code;
	}

}
