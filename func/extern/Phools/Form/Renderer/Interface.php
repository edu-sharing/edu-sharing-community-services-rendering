<?php

/**
 *
 *
 *
 */
interface Phools_Form_Renderer_Interface
{

	/**
	 *
	 * @param string $Name
	 *
	 * @return string
	 */
	public function startDivision($Name);

	/**
	 *
	 * @param Phools_Form_Element_Form $Form
	 *
	 * @return string
	 */
	public function stopDivision();

	/**
	 *
	 * @param string $Name
	 *
	 * @return string
	 */
	public function startFieldset($Name);

	/**
	 *
	 * @param Phools_Form_Element_Form $Form
	 *
	 * @return string
	 */
	public function stopFieldset();

	/**
	 *
	 * @param string $Name
	 * @param string $Action
	 * @param string $Method
	 *
	 * @return string
	 */
	public function startForm($Name, $Action, $Method);

	/**
	 *
	 * @param Phools_Form_Element_Form $Form
	 *
	 * @return string
	 */
	public function stopForm();

	/**
	 *
	 * @param string $Name
	 *
	 * @return string
	 */
	public function startParagraph($Name);

	/**
	 *
	 * @param Phools_Form_Element_Form $Form
	 *
	 * @return string
	 */
	public function stopParagraph();

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 *
	 * @return string
	 */
	public function renderHidden($Name, $Value);

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 *
	 * @return string
	 */
	public function renderSubmit($Name, $Value);

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 *
	 * @return string
	 */
	public function renderReset($Name, $Value);

	/**
	 *
	 * @param string $Name
	 * @param array $Options
	 *
	 * @return string
	 */
	public function renderRadio($Name, array $Options);

	/**
	 *
	 * @param string $Name
	 * @param bool $IsMultiple
	 * @param int $Size
	 * @param array $Options
	 *
	 * @return string
	 */
	public function renderList($Name, $IsMultiple, $Size, array $Options);

	/**
	 *
	 * @param string $Name
	 * @param array $Options
	 *
	 * @return string
	 */
	public function renderCheckbox($Name, array $Options);

	/**
	 *
	 * @param string $Name
	 * @param string $Content
	 * @param int $Rows
	 * @param int $Columns
	 *
	 * @return string
	 */
	public function renderTextarea($Name, $Content, $Rows, $Columns);

	/**
	 *
	 * @param string $Name
	 * @param string $Content
	 * @param int $Length
	 *
	 * @return string
	 */
	public function renderTextfield($Name, $Content, $Length);

}
