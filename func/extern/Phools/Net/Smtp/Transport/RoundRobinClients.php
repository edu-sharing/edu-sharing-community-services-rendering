<?php

/**
 * Transport message by rotating through a list of clients to enable simple
 * load-balancing.
 *
 *
 */
class Phools_Net_Smtp_Transport_RoundRobinClients
implements
	Phools_Net_Smtp_Transport_Interface
{

	/**
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 */
	public function __construct(
		Phools_Net_Smtp_Client_Interface $Client,
		$MessagesPerClient = 10)
	{
		$this
			->addClient($Client)
			->setMessagesPerClient($MessagesPerClient);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Clients = array();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Transport_Interface::send()
	 */
	public function send(Phools_Net_Smtp_Message_Interface $Message, $From, array $To)
	{
		// rotate if NO remainder present
		if ( ! $this->getSentMessagesCounter() % $this->getMessagesPerClient() )
		{
			$Client = array_unshift($this->Clients);
			array_push($this->Clients, $Client);
		}

		$Client
			->initialize()
			->send()
			->terminate();

		$this->increaseSentMessagesCounter();

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Client_Interface
	 */
	private $Clients = array();

	/**
	 *
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Clients
	 * @return Phools_Net_Smtp_Transport_RoundRobin
	 */
	public function addClient(Phools_Net_Smtp_Client_Interface $Client)
	{
		$this->Clients[] = $Client;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Smtp_Client_Interface
	 */
	protected function getClients()
	{
		return $this->Clients;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $MessagesPerClient = 1;

	/**
	 *
	 *
	 * @param int $MessagesPerClient
	 * @return Phools_Net_Smtp_Transport_RoundRobinClients
	 */
	public function setMessagesPerClient($MessagesPerClient)
	{
		if ( 1 > $MessagesPerClient )
		{
			throw new Phools_Net_Smtp_Exception('Must be positive integer.');
		}

		$this->MessagesPerClient = (int) $MessagesPerClient;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMessagesPerClient()
	{
		return $this->MessagesPerClient;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $SentMessagesCounter = 0;

	/**
	 *
	 *
	 * @return Phools_Net_Smtp_Transport_RoundRobinClients
	 */
	protected function increaseSentMessagesCounter()
	{
		$this->SentMessagesCounter++;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getSentMessagesCounter()
	{
		return $this->SentMessagesCounter;
	}

}
