<?php
// A really simple json RPC interface

require_once('config.php');
require_once('DB.php');

$rpc = new JSON_RPC(new MessageService());
echo $rpc->getResponse($_POST['request']);

class JSON_RPC {
	
	protected $service;
	
	function __construct($obj) {
		$this->service = $obj;
	}
	function getResponse($request_string) {
		$request = json_decode($request_string,true);
		$response = array('error'=>null);
		
		if($request['id'])
			$response['id'] = $request['id'];

		if(method_exists($this->service,$request['method'])) {
			try {
				$r = call_user_method_array($request['method'],$this->service,$request['params']);
				$response['result'] = $r;
			} catch (Exception $e) {
				$response['error'] = array('code' => -31000,'message' => $e->getMessage());
			}
		} else {
			$response['error'] = array('code' => -32601,'message' => 'Procedure not found.');
		}

		return json_encode($response);
	}
}

class MessageService {
	function __construct() {
		
	}
	function getMessages($id) {
		$q = new Query();
		return $q->sql("SELECT * FROM simplepo_messages WHERE catalogue_id=?",$id)->fetchAll();
	}
	function makeError() {
		throw new Exception("This is an error");
	}
}
