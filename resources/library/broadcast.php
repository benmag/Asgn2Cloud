<?php
/**
 * This class allows PHP to communicate with socket.io 
 * 
 * Once a tweet has been processed, we send it back to the client 
 */
class broadcast {
	
	/** 
	 * Send a message to the socket.io (node) server 
	 *
	 * @param $data the data to send
	 * @return void
	 */
	public function send($data) {

		require_once(dirname(__FILE__)."/../../config.php");
		require_once(dirname(__FILE__)."/elephant.io/Client.php");
		//use ElephantIO\Client as ElephantIOClient;

		$elephant = new ElephantIO\Client(NODE_SERVER, 'socket.io', 1, false, true, true);
	
		$elephant->init();
		$elephant->send(
			ElephantIO\Client::TYPE_EVENT,
			null,
			null,
			json_encode(
					array('name' => 'broadcast_twitter', 
						  'args' => $data
						)
			)
		);
		$elephant->close();
	}
	
}