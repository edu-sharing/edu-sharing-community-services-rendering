<?php

require_once(dirname(__FILE__).'/RenderObject.php');

/**
 * Form must be valid to allow rendering of a edusharing-resource.
 *
 *
 */
class ESRender_Form_RenderResource
extends ESRender_Form_RenderObject
{

	public function __construct($Name = "render_resource")
	{
		parent::__construct($Name);

		$ApplicationId = new Phools_Form_Element_Textfield('app_id');
		$ApplicationId->setValidator(new ESRender_Validator_ApplicationId());
		$this->appendComponent($ApplicationId);

		$ResourceId = new Phools_Form_Element_Textfield('resource_id');
		$ResourceId->setValidator(new ESRender_Validator_ObjectId());
		$this->appendComponent($ResourceId);

		$CourseId = new Phools_Form_Element_Textfield('course_id');
		$CourseId->setValidator(new ESRender_Validator_CourseId());
		$this->appendComponent($CourseId);
	}

}
