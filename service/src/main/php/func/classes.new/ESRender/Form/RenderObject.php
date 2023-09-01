<?php

require_once(dirname(__FILE__).'/../Validator/ApplicationId.php');
require_once(dirname(__FILE__).'/../Validator/CourseId.php');
require_once(dirname(__FILE__).'/../Validator/DisplayMode.php');
require_once(dirname(__FILE__).'/../Validator/ObjectId.php');
require_once(dirname(__FILE__).'/../Validator/ResourceId.php');
require_once(dirname(__FILE__).'/../Validator/SessionId.php');
require_once(dirname(__FILE__).'/../Validator/Theme.php');
require_once(dirname(__FILE__).'/../Validator/Username.php');
require_once(dirname(__FILE__).'/../Validator/Version.php');

/**
 * Form must be valid to allow rendering of a edusharing-object.
 *
 *
 */
class ESRender_Form_RenderObject
extends Phools_Form_Element_Form
{

	public function __construct($Name = "render_object")
	{
		parent::__construct($Name);

		$ObjectId = new Phools_Form_Element_Textfield('obj_id');
		$ObjectId->setValidator(new ESRender_Validator_ObjectId());
		$this->appendComponent($ObjectId);

		$RepositoryId = new Phools_Form_Element_Textfield('rep_id');
		$RepositoryId->setValidator(new ESRender_Validator_ApplicationId());
		$this->appendComponent($RepositoryId);

		$SessionId = new Phools_Form_Element_Textfield('session');
		$SessionId->setValidator(new ESRender_Validator_SessionId());
		$this->appendComponent($SessionId);

		$Username = new Phools_Form_Element_Textfield('u');
		$Username->setValidator(new ESRender_Validator_Username());
		$this->appendComponent($Username);

		$Embed = new Phools_Form_Element_Checkbox('embed');
		$Embed->addFilter(new Phools_Filter_Boolean());
		$this->appendComponent($Embed);

		$Theme = new Phools_Form_Element_Textfield('theme');
		$Theme->setValidator(new ESRender_Validator_Theme());
		$this->appendComponent($Theme);
	}

}
