#!/usr/bin/php
<?php
set_time_limit(0);
ini_set('display_errors', 'on');
$configure = array(
	'server' => 'irc.twitch.tv',
	'port' => 6667,
	'nick' => 'Abotnickname',
	'name' => 'IRC Bot',
	'channel' => '',
	'pass' => ''
);
class IRCBot{
	//TCP connection holder.
	public $socket;
	
	//Message holder.
	public $msg = array();
	
	/*
	 * Constucter.
	 * Opens the server connection, and logs in the bot.
	 * 
	 * @param array.
	 */
	function __construct($configure){
		 $this->socket = fsockopen($configure['server'], $configure['port']);
		 $this->login($configure);
		 $this->main();
		 $this->send_data('JOIN', $this->configure['channel']);
	}
	
	/*
	 * Logs bot in to server
	 * 
	 * @param array.
	 */
	 
	function login($configure){
		$this->send_data('USER', $configure['nick'] . ' some domain.com ' . $configure['nick'] . ' :' . $configure['name']);
		$this->send_data('NICK', $configure['nick']);
	}
	
	/*
	 * Main function, used to grab all data.
	 * 
	 */
	
	function main(){
		while (true):
			$data = fgets($this->socket, 128);
			flush();
			$this->ex = explode(' ', $data);
		
			if($this->ex[0] == 'PING'){
				//Plays ping-pong with the server..
				$this->send_data('PONG', $this->ex[1]);
			}
			$command = str_replace(array(chr(10), chr(13)), '', $this->ex[3]);
		
			//List of commands the bot responds to.
			switch($command){
				case ':!say':
					$this->say();
				break;
				case ':!join':
					$this->join_channel($this->ex[4]);
				break;
			
				case ':!quit':
					$this->send_data('QUIT', $this->ex[4]);
				break;
			
				case ':!op':
					$this->op_user();
				break;	
			
				case ':!deop':
					$this->op_user('','', false);
				break;

				case ':!time':
					$this->send_data("PRIVMSG", "Time" . date("F j, Y, g:i a" . time()));
				break;


				case 'Hello':
					fputs($this->socket,"PRIVMSG " . $this->ex[2] . " : Hi!\n");
				break;
			
			}
		endwhile;
	}
	function say(){
		$arraysize = sizeof($this->ex);
		//1,2,3 are just nick and chan, 4 is where text starts 
		$count = 4;
		while($count <= $arraysize) {
			$text = $text . " " . $this->ex[$count];
			$count++;
		}
		$this->privmsg($text, $this->ex[2]);
		unset($text);
	}
	
	function privmsg($message, $to){
		fputs($this->socket,"PRIVMSG " . $to . " :" . $message . "\n");
	}
					
	
	/*
	 * Sends data to the server.
	 */
	function send_data($cmd, $msg = null){
		 if($msg == null){
			 fputs($this->socket, $cmd . "\n");
			 echo "<b>$cmd</b>";
		 }else{
			fputs($this->socket, $cmd.' '.$msg."\n");
			echo "<b>$cmd $msg</b>";
		 }
	}
	
	/*
	 * Joins a channel.
	 * 
	 * @param text
	 */
	function join_channel($channel){
		$this->send_data('JOIN', $channel);
	}
	/*
	 * Give/Take operator status.
	 */ 
	function op_user($channel = '', $user = '', $op = true){
		if($channel == '' || $user == ''){
			if($channel == ''){
				$channel = $this->ex[2];
			}
			if($user == ''){
				 $user = strstr($this->ex[0], '!', true);
			};
			if($op){
				$this->send_data('MODE', $channel . ' +o ' . $user);
			}else{
				$this->send_data('MODE', $channel . ' -o ' . $user);
			}
		}
	}
}
$bot = new IRCBot($configure);
		
?>

