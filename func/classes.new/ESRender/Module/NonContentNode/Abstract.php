<?php

/**
 * Base-class for all modules handling objects, which don't contain any
 * binary data, e.g. URL's.
 *
 *
 */
abstract class ESRender_Module_NonContentNode_Abstract
extends ESRender_Module_Base
{

	/**
	 * Modules of this type have no binary content to download anyway, so
	 * the default action on download has to be dynamic().
	 *
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::download()
	 */
	protected function download()
	{
		return $this->dynamic();
	}

	/**
	 * Modules of this type have no binary content to download anyway, so
	 * the default action on download has to be display().
	 *
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::download()
	 */
	protected function inline()
	{
		return $this->dynamic();
	}

}
