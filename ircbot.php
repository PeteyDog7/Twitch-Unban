<?php

//So the bot doesnt stop.

set_time_limit(0);

ini_set('display_errors', 'on');


	//Example connection stuff.

	$config = array( 
		'server' => 'irc.twitch.tv', 
		'port' => 6667, 
		'nick' => 'unbanbot', 
		'name' => 'unbanbot', 
		'pass' => 'oauth:zvp4fad4i4fen470c14v588z3vx0zu', 
	);
	  

class IRCBot {
    
	//This is going to hold our TCP/IP connection

	var $socket;



	//This is going to hold all of the messages both server and client

	var $ex = array();



	/*

	 Construct item, opens the server connection, logs the bot in



	 @param array

	*/

	function __construct($config)

	{

		$this->socket = fsockopen($config['server'], $config['port']);

		$this->login($config);
        
        $this->send_data('JOIN', '#Peteydog7');

		$this->main();

	}



	/*

	 Logs the bot in on the server



	 @param array

	*/

	function login($config)

	{

		$this->send_data('PASS', $config['pass']);

		$this->send_data('NICK', $config['nick']);

	}



	/*

	 This is the workhorse function, grabs the data from the server and displays on the browser

	*/

	function main(){
        
        fputs($this->socket,"PRIVMSG " . "#Peteydog7" . " :" . "I have been inititated!" . "\n");

        for ($i = 1; $i<100; $i++) {
        
		$data = fgets($this->socket, 128);

		echo nl2br($data);

		flush();

		$this->ex = explode(' ', $data);



		if($this->ex[0] == 'PING')

		{

			$this->send_data('PONG', $this->ex[1]); //Plays ping-pong with the server to stay connected.

		}



		$command = str_replace(array(chr(10), chr(13)), '', $this->ex[3]);



		switch($command) //List of commands the bot responds to from a user.

		{
            
            case ':!test':
            
                $this->say();
            
                break;

			case ':!join':

				$this->join_channel($this->ex[4]);

				break;



			case ':!quit':

				$this->send_data('QUIT', 'noahstech.net made Bot');


				break;

            /*

			case ':!op':

				$this->op_user();

				break;



			case ':!deop':

				$this->op_user('','', false);

				break;



			case ':!protect':

				$this->protect_user();

				break;

            */
            
		}

        }
            
	}



	function send_data($cmd, $msg = null) //displays stuff to the broswer and sends data to the server.

	{

		if($msg == null)

		{

			fputs($this->socket, $cmd."\r\n");

			echo '<strong>'.$cmd.'</strong><br />';

		} else {

			fputs($this->socket, $cmd.' '.$msg."\r\n");

			echo '<strong>'.$cmd.' '.$msg.'</strong><br />';

		}

	}



	function join_channel($channel) //Joins a channel, used in the join function.

	{

		if(is_array($channel))

		{

			foreach($channel as $chan)

			{

				$this->send_data('JOIN', $chan);

			}

		} else {

			$this->send_data('JOIN', $channel);

		}

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

	function protect_user($user = '')

	{

		if($user == '')

		{

            if(php_version() >= '5.3.0') {
				$user = strstr($this->ex[0], '!', true);
			} else {
				$length = strstr($this->ex[0], '!');
				$user   = substr($this->ex[0], 0, $length);
			}
        }

    }



		$this->send_data('MODE', $this->ex[2] . ' +a ' . $user);

	}

			

	function op_user($channel = '', $user = '', $op = true)

	{

		if($channel == '' || $user == '')

		{

			if($channel == '')

			{

				$channel = $this->ex[2];

			}



			if($user == '')

			{

            if(php_version() >= '5.3.0')
			{
				$user = strstr($this->ex[0], '!', true);
			} else {
				$length = strstr($this->ex[0], '!');
				$user   = substr($this->ex[0], 0, $length);
			}
			}

		}



		if($op)

		{

			$this->send_data('MODE', $channel . ' +o ' . $user);

		} else {

			$this->send_data('MODE', $channel . ' -o ' . $user);

		}

	}
    */

}


	$bot = new IRCBot($config);

?>

