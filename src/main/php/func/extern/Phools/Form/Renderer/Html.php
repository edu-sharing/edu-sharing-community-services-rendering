<?php

/**
 *
 *
 *
 */
class Phools_Form_Renderer_Html
implements Phools_Form_Renderer_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::startDivision()
	 */
	public function startDivision($Name)
	{
		$String = '<div>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::stopDivision()
	 */
	public function stopDivision()
	{
		$String = '</div>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::startFieldset()
	 */
	public function startFieldset($Name)
	{
		$String = '<fieldset>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::stopFieldset()
	 */
	public function stopFieldset()
	{
		$String = '</fieldset>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::startParagraph()
	 */
	public function startParagraph($Name)
	{
		$String = '<p>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::stopParagraph()
	 */
	public function stopParagraph()
	{
		$String = '</p>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::startForm()
	 */
	public function startForm($Name, $Action, $Method)
	{
		$String = '<form name="'.htmlentities($Name).'" action="'.htmlentities($Action).'" method="'.htmlentities($Method).'">';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::stopForm()
	 */
	public function stopForm()
	{
		$String = '</form>';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderSubmit()
	 */
	public function renderSubmit($Name, $Value)
	{
		$String = '<input type="submit" name="'.htmlentities($Name).'" value="'.htmlentities($Value).'" />';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderHidden()
	 */
	public function renderHidden($Name, $Value)
	{
		$String = '<input type="hidden" name="'.htmlentities($Name).'" value="'.htmlentities($Value).'" />';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderReset()
	 */
	public function renderReset($Name, $Value)
	{
		$String = '<input type="reset" name="'.htmlentities($Name).'" value="'.htmlentities($Value).'" />';
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderTextarea()
	 */
	public function renderTextarea($Name, $Content, $Rows, $Columns)
	{
		$String = '<textarea name="'.htmlentities($Name).'" rows="'.htmlentities($Rows).'" columns="'.htmlentities($Columns).'">';
		$String .= $Content;
		$String .= '</textarea>';

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderCheckbox()
	 */
	public function renderList($Name, $IsMultiple, $Size, array $Options)
	{
		$String = '<select name="'.htmlentities($Name.'[]').'" '. ($IsMultiple ? 'multiple="multiple"' : '') .' size="'.htmlentities($Size).'">';

		while( $Option = array_shift($Options) )
		{
			$String .= '<option';
			$String .= $Option->getValue() ? ' value="'.htmlentities($Option->getValue()).'"' : '';
			$String .= $Option->isSelected() ? ' checked="checked"' : '';
			$String .= '><span class="name">'.$Option->getName().'</span></option>';
		}

		$String .= '</select>';

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderCheckbox()
	 */
	public function renderCheckbox($Name, array $Options)
	{
		$String = '';

		while( $Option = array_shift($Options) )
		{
			$String .= '<input type="checkbox" name="'.htmlentities($Name).'[]"';
			$String .= $Option->getValue() ? ' value="'.htmlentities($Option->getValue()).'"' : '';
			$String .= $Option->isSelected() ? ' checked="checked"' : '';
			$String .= '><span class="name">'.htmlentities($Option->getName()).'</span></input>';
			$String .= '</input>';
		}

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderRadio()
	 */
	public function renderRadio($Name, array $Options)
	{
		$String = '';

		while( $Option = array_shift($Options) )
		{
			$String .= '<input type="radio" name="'.htmlentities($Name).'"';
			$String .= $Option->getValue() ? ' value="'.htmlentities($Option->getValue()).'"' : '';
			$String .= $Option->isSelected() ? ' checked="checked"' : '';
			$String .= '><span class="name">'.htmlentities($Option->getName()).'</span></input>';
		}

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Renderer_Interface::renderTextfield()
	 */
	public function renderTextfield($Name, $Content, $Length)
	{
		$String = '<input type="text" name="'.htmlentities($Name).'" value="'.$Content.'" length="'.htmlentities($Length).'">';
		return $String;
	}

}
