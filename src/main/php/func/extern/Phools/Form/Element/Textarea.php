<?php

/**
 * Implementing a textarea-element.
 *
 * @see http://www.w3.org/TR/html4/interact/forms.html#h-17.7
 *
 *
 */
class Phools_Form_Element_Textarea
extends Phools_Form_Component_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param int $Columns
	 * @param int $Rows
	 */
	public function __construct($Name, $Columns = 5, $Rows = 80)
	{
		parent::__construct($Name);

		$this
			->setColumns($Columns)
			->setRows($Rows);
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
		return $Renderer->renderTextarea(
			$this->getPath(),
			$this->getText(),
			$this->getRows(),
			$this->getColumns());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::isValid()
	 */
	public function isValid()
	{
		$Validator = $this->getValidator();
		if ( $Validator )
		{
			return $Validator->validate($this->getText());
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
	 * @return Phools_Form_Element_Textarea
	 */
	public function setText($Text)
	{
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
	private $Columns = 80;

	/**
	 *
	 *
	 * @param int $Columns
	 * @return Phools_Form_Element_Textarea
	 */
	public function setColumns($Columns)
	{
		$this->Columns = (int) $Columns;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getColumns()
	{
		return $this->Columns;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $Rows = 5;

	/**
	 *
	 *
	 * @param int $Rows
	 * @return Phools_Form_Element_Textarea
	 */
	public function setRows($Rows)
	{
		$this->Rows = (int) $Rows;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getRows()
	{
		return $this->Rows;
	}

}
