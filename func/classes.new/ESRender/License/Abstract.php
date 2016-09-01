<?php

/**
 *
 *
 */
abstract class ESRender_License_Abstract
implements ESRender_License_Interface
{

	/**
	 *
	 * @param string $Name
	 * @param string $Author
	 * @param string $Url
	 * @param string $IconUrl
	 */
	public function __construct($Name, $Author, $IconUrl = '', $permalink, $fileName)
	{
		$this
			->setName($Name)
			->setAuthor($Author)
			->setIconUrl($IconUrl)
            ->setPermalink($permalink)
            ->setFileName($fileName);
	}

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
	 * @return ESRender_License_Abstract
	 */
	public function setName($Name)
	{
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		$this->Name = $Name;

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
	 *
	 * @var string
	 */
	protected $Author = '';

	/**
	 *
	 *
	 * @param string $Author
	 * @return ESRender_License_Abstract
	 */
	public function setAuthor($Author)
	{
		assert( is_string($Author) );
		assert( 0 < strlen($Author) );

		$this->Author = $Author;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->Author;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Url = '';

	/**
	 *
	 *
	 * @var string
	 */
	protected $IconUrl = '';

	/**
	 *
	 *
	 * @param string $IconUrl
	 * @return ESRender_License_Abstract
	 */
	public function setIconUrl($IconUrl)
	{
		$this->IconUrl = $IconUrl;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getIconUrl()
	{
		return $this->IconUrl;
	}
    
    protected $permalink = '';
    public function setPermalink($permalink) {
        $this -> permalink = $permalink;
        return $this;
    }
    public function getPermalink() {
        return $this -> permalink;
    }
    
    protected $fileName = '';
    public function setFilename($fileName) {
        $this -> fileName = $fileName;
        return $this;
    }
    public function getFileName() {
        return $this -> fileName;
    }

}
