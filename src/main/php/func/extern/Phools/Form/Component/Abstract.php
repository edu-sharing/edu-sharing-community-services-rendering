<?php

/**
 *
 *
 *
 */
abstract class Phools_Form_Component_Abstract
{

	/**
	 *
	 * @param string $Name This components name.
	 */
	public function __construct($Name)
	{
		$this->setName($Name);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Filters = null;
		$this->NextComponent = null;
		$this->PreviousComponent = null;
		$this->ParentComponent = null;
	}

	/**
	 * Tell if form-element is valid, a.k.a. has (a) valid value(s).
	 *
	 * @return bool
	 */
	abstract public function isValid();

	/**
	 * Populate (or fill) the form with given values, e.g. $_POST.
	 *
	 * @param array $Values
	 */
	abstract public function populate(array $Values);

	/**
	 * Render this component using given $Renderer technique.
	 *
	 * @param Phools_Form_Renderer_Interface $Renderer
	 *
	 * @return string
	 */
	abstract public function render(Phools_Form_Renderer_Interface $Renderer);

	/**
	 *
	 *
	 * @var string
	 */
	protected $Name = '';

	/**
	 *
	 *
	 * @param string $Name
	 * @return Phools_Form_Component_Abstract
	 */
	protected function setName($Name)
	{
		$this->Name = (string) $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

	/**
	 *
	 * @param string $Separator
	 *
	 * @return string
	 */
	public function getPath($Separator = '_')
	{
		$Path = '';

		$ParentComponent = $this->getParentComponent();
		if ( $ParentComponent )
		{
			$Path = $ParentComponent->getPath($Separator);
			$Path .= $Separator;
		}

		$Path .= $this->getName();

		return $Path;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Id = '';

	/**
	 * @see http://www.w3.org/TR/html4/sgml/dtd.html#coreattrs
	 *
	 * @param string $Id
	 * @return Phools_Form_Component_Abstract
	 */
	public function setId($Id)
	{
		$this->Id = (string) $Id;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getId()
	{
		return $this->Id;
	}

	/**
	 *
	 *
	 * @var array
	 */
	protected $Classes = array();

	/**
	 * @see http://www.w3.org/TR/html4/sgml/dtd.html#coreattrs
	 *
	 * @param string $Class
	 * @return Phools_Form_Component_Abstract
	 */
	public function addClass($Class)
	{
		$this->Classes[] = (string) $Classes;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getClasses()
	{
		return $this->Classes;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Title = '';

	/**
	 * @see http://www.w3.org/TR/html4/sgml/dtd.html#coreattrs
	 *
	 * @param string $Title
	 * @return Phools_Form_Component_Abstract
	 */
	public function setTitle($Title)
	{
		$this->Title = (string) $Title;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getTitle()
	{
		return $this->Title;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Lang = '';

	/**
	 * @see http://www.w3.org/TR/html4/sgml/dtd.html#i18n
	 *
	 * @param string $Lang
	 * @return Phools_Form_Component_Abstract
	 */
	public function setLang($Lang)
	{
		$this->Lang = (string) $Lang;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getLang()
	{
		return $this->Lang;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $TextDirection = Phools_Form_TextDirection::LEFT_TO_RIGHT;

	/**
	 * @see http://www.w3.org/TR/html4/sgml/dtd.html#i18n
	 *
	 * @param string $TextDirection
	 * @return Phools_Form_Component_Abstract
	 */
	public function setTextDirection($TextDirection)
	{
		$this->TextDirection = (string) $TextDirection;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getTextDirection()
	{
		return $this->TextDirection;
	}

	/**
	 *
	 * @var array
	 */
	private $Filters = array();

	/**
	 * Add a input-filter.
	 *
	 * @param Phools_Filter_Interface $Filter
	 *
	 * @return Phools_Form_Component_Abstract
	 */
	public function addFilter(Phools_Filter_Interface $Filter)
	{
		$this->Filters[] = $Filter;
		return $this;
	}

	/**
	 * Return an array of this component's filters.
	 *
	 * @return array
	 */
	protected function getFilters()
	{
		return $this->Filters;
	}

	/**
	 *
	 *
	 * @var Phools_Validator_Interface
	 */
	private $Validators = array();

	/**
	 *
	 *
	 * @param Phools_Validator_Interface $Validator
	 * @return Phools_Form_Component_Abstract
	 */
	public function addValidator(Phools_Validator_Interface $Validator)
	{
		$this->Validators[] = $Validator;
		return $this;
	}

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	protected function getValidators()
	{
		return $this->Validators;
	}

	/**
	 *
	 *
	 * @var Phools_Form_Composite_Abstract
	 */
	private $ParentComponent = null;

	/**
	 *
	 *
	 * @param Phools_Form_Composite_Abstract $ParentComponent
	 * @return Phools_Form_Component_Abstract
	 */
	protected function setParentComponent(Phools_Form_Composite_Abstract $ParentComponent)
	{
		$this->ParentComponent = $ParentComponent;
		return $this;
	}

	/**
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function getParentComponent()
	{
		return $this->ParentComponent;
	}

	/**
	 *
	 *
	 * @var Phools_Form_Component_Abstract
	 */
	private $PreviousComponent = null;

	/**
	 *
	 *
	 * @param Phools_Form_Component_Abstract $PreviousComponent
	 * @return Phools_Form_Component_Abstract
	 */
	protected function setPreviousComponent(
		Phools_Form_Component_Abstract $PreviousComponent)
	{
		$this->PreviousComponent = $PreviousComponent;
		return $this;
	}

	/**
	 *
	 * @return Phools_Form_Component_Abstract
	 */
	public function getPreviousComponent()
	{
		return $this->PreviousComponent;
	}

	/**
	 *
	 *
	 * @var Phools_Form_Component_Abstract
	 */
	private $NextComponent = null;

	/**
	 *
	 *
	 * @param Phools_Form_Component_Abstract $NextComponent
	 * @return Phools_Form_Component_Abstract
	 */
	protected function setNextComponent(
		Phools_Form_Component_Abstract $NextComponent)
	{
		$this->NextComponent = $NextComponent;
		return $this;
	}

	/**
	 *
	 * @return Phools_Form_Component_Abstract
	 */
	public function getNextComponent()
	{
		return $this->NextComponent;
	}

}
