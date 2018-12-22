<?php

use use React\Stream\CompositeStream;

class Fetcher {

	public function __construct($client, $loop)
	{
		//reactphp httpclient object
		$this->client = $client;
		$this->loop = $loop;	
	}

	public function download($id, $url)
	{
		//absolute path + image name 
		$img = __DIR__ . DIRECTORY_SEPARATOR . "images". 
			DIRECTORY_SEPARATOR . $id . ".jpg";
	
		if(! file_exists($img))
		{
			echo "url: " . $url . PHP_EOL;
			$out = new CompositeStream(fopen($img, "w"), $this->loop);
			
			$req = $this->client->request("GET", $url);
			
			$req->on("response", function($res) use ($out){

				$res->on("data", function($chunk) use ($out) {
					$out->write($chunk);
				});

				$res->on("end", function() use ($out) {
					$out->end();
				});

			});

			$req->end();

			/**
			$f = fopen($img, "w");
			fwrite($f, file_get_contents($url));
			fclose($f);
			**/
		}
	}

}
