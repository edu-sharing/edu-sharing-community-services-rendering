<?php

/**
 *
 *
 */
class Phools_Template_Script
extends Phools_Template_Abstract
{

	/**
	 *
	 * @param string $Directory
	 * @param string $FileExtension
	 */
	public function __construct($Directory = '.', $FileExtension = '.phtml')
	{
		$this->setDirectory($Directory)
			->setFileExtension($FileExtension);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Template_Abstract::render()
	 */
	public function render($Name, array $Params = array())
	{
		$Filename = $this->getDirectory() . DIRECTORY_SEPARATOR;

		$Theme = $this->getTheme();
		if ( $Theme )
		{
			$Filename .= $Theme . DIRECTORY_SEPARATOR;
		}

		$Filename .= str_replace('/', DIRECTORY_SEPARATOR, $Name);
		$Filename .= $this->getFileExtension();

		$Filename = realpath($Filename);
		if ( ! $Filename )
		{
			throw new Phools_Template_Exception_TemplateNotFoundException('Template "'.$Name.'" not found.');
		}

		if ( file_exists($Filename) )
		{
			// make $Params visible in template-script
			extract($Params);

			ob_start();

			if ( ! include($Filename) )
			{
				ob_end_clean();
				throw new Phools_Template_Exception_TemplateNotFoundException('Error including template-file "'.$Filename.'".');
			}

			$String = ob_get_clean();

			return $String;
		}

		return null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Directory = '.';

	/**
	 *
	 *
	 * @param string $Directory
	 * @return Phools_Template_Filesystem
	 */
	public function setDirectory($Directory)
	{
		$this->Directory = (string) $Directory;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getDirectory()
	{
		return $this->Directory;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $FileExtension = '.phtml';

	/**
	 *
	 *
	 * @param string $FileExtension
	 * @return Phools_Template_Filesystem
	 */
	public function setFileExtension($FileExtension)
	{
		$this->FileExtension = (string) $FileExtension;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getFileExtension()
	{
		return $this->FileExtension;
	}

}
