<?php

class Fetcher {

	public function __construct($client)
	{
		//reactphp httpclient object
		$this->client = $client;	
	}

	public function download($id, $url)
	{
		$img = __DIR__ . DIRECTORY_SEPARATOR . "images". 
			DIRECTORY_SEPARATOR . $id . ".jpg";
	
		echo $img . PHP_EOL;
	
		if(! file_exists($img))
		{
			echo "url: " . $url . PHP_EOL;

			$f = fopen($img, "w");
			fwrite($f, file_get_contents($url));
			fclose($f);

#			$req = $this->client->request("GET", $img);
#			
#			$req->on("response", function($res){
#
#				$res->on("data", function($chunk){
#					echo $chunk;
#					fwrite($f, $chunk);
#				});	
#
#				$res->on("end", function(){
#					fclose($f);
#				});
#
#			});
#
#			$req->end();

		}
	}

}
