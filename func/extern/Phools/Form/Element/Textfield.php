<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Textfield
extends Phools_Form_Component_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param int $Length
	 */
	public function __construct($Name, $Length = 55)
	{
		parent::__construct($Name);

		$this
			->setLength($Length);
	}

	public function populate(array $Values)
	{
		$Name = $this->getPath();
		if ( isset($Values[$Name]) )
		{
			$this->setText($Values[$Name]);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		return $Renderer->renderTextfield(
			$this->getPath(),
			$this->getText(),
			$this->getLength());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::isValid()
	 */
	public function isValid()
	{
		$Value = $this->getText();

		foreach( $this->getValidators() as $Validator )
		{
			if ( ! $Validator->validate($Value) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Text = '';

	/**
	 *
	 *
	 * @param string $Text
	 *
	 * @return Phools_Form_Element_Textarea
	 */
	public function setText($Text)
	{
		foreach( $this->getFilters() as $Filter )
		{
			$Text = $Filter->filter($Text);
		}

		$this->Text = (string) $Text;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getText()
	{
		return $this->Text;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $Length = 80;

	/**
	 *
	 *
	 * @param int $Length
	 * @return Phools_Form_Element_Textarea
	 */
	public function setLength($Length)
	{
		$this->Length = (int) $Length;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getLength()
	{
		return $this->Length;
	}

}
