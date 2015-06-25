#!/usr/bin/php
<?php
set_time_limit(0);
ini_set('display_errors', 'on');
$configure = array( 
		'server' => 'irc.twitch.tv', 
		'port' => 6667, 
		'nick' => 'unbanbot', 
		'name' => 'unbanbot', 
		'pass' => 'oauth:zvp4fad4i4fen470c14v588z3vx0zu',
        'channel' => '#Peteydog7',
	);
class IRCBot{
	//TCP connection holder.
	public $socket;
	
	//Message holder.
	public $msg = array();
    
    public $configure = array();
    
	/*
	 * Constucter.
	 * Opens the server connection, and logs in the bot.
	 * 
	 * @param array.
	 */
	function __construct($configure){
         echo 'test\n';
         $this->configure = $configure;
		 $this->socket = fsockopen($this->configure['server'], $this->configure['port']);
		 $this->login($configure);
         $this->send_data('JOIN', $this->configure['channel']);
		 $this->main();
	}
	
	/*
	 * Logs bot in to server
	 * 
	 * @param array.
	 */
	 
	function login($configure){
		$this->send_data('PASS', $this->configure['pass']);
		$this->send_data('NICK', $this->configure['nick']);
	}
	
	/*
	 * Main function, used to grab all data.
	 * 
	 */
	
	function main(){
        fputs($this->socket,"PRIVMSG " . $this->configure['channel']. " :" . "I have been inititated!" . "\n");
		while (true):
			$data = fgets($this->socket, 128);
			flush();
			$this->msg = explode(' ', $data);
		
			if($this->msg[0] == 'PING'){
				//Plays ping-pong with the server..
				$this->send_data('PONG', $this->msg[1]);
			}
            
        if(isset($this->msg[3]) {
			$command = str_replace(array(chr(10), chr(13)), '', $this->msg[3]);
            var_dump($this->msg);
            echo '   ---  ';
        }
		
			//List of commands the bot responds to.
			switch($command){
				case ':!say':
					$this->say();
				break;
				case ':!join':
					$this->join_channel($this->msg[4]);
				break;
			
				case ':!quit':
					$this->send_data('QUIT', $this->msg[4]);
				break;

				case ':!time':
					$this->send_data("PRIVMSG", "Time" . date("F j, Y, g:i a" . time()));
				break;


				case 'Hello':
					fputs($this->socket,"PRIVMSG " . $this->configure['channel'] . " : Hi!\n");
				break;
			
			}
		endwhile;
	}
    
	function say(){
		$arraysize = sizeof($this->msg);
		//1,2,3 are just nick and chan, 4 is where tmsgt starts 
		$count = 4;
		while($count <= $arraysize) {
			$text = $text . " " . $this->msg[$count];
			$count++;
		}
		$this->privmsg($text, $this->configure['channel']);
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
}
$bot = new IRCBot($configure);
		
?>

