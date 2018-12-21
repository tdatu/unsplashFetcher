<?php

use Noodlehaus\Config;
use Noodlehaus\Parser\Json;
use React\HttpClient\Client;
use React\HttpClient\Response;


require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/fetcher.php";

$config = new Config(__DIR__ . "/config.ini");
$url = $config->get("unsplash.url");
$access_key = $config->get("unsplash.access_key");
$categories = explode(",",$config->get("unsplash.categories"));
$freq = $config->get("unsplash.freq");
$index = 0;

$loop = React\EventLoop\Factory::create();

//Prep the request
$headers = [
	"Content-Type" => "application/json",
	"Authorization" => "Client-ID " . $access_key 
];

$client = new Client($loop);
$fetcher = new Fetcher($client);

$loop->addPeriodicTimer($freq, function() use ($client,$url,&$headers,$categories, &$index, &$fetcher){
	
	if($index < count($categories)){

		echo "Cat: " . $categories[$index] . "\n";

		$request = $client->request("GET", $url . "search/photos?query=" . $categories[$index], $headers);
	
		$request->on("response", function($response) use (&$fetcher){
			
			$response->on("data", function($chunk) use (&$fetcher) {
				$json = json_decode($chunk);
				if(! isset($json->{'errors'})){
					foreach($json->{'results'} as $photo){
					
						//var_dump($photo);
						$fetcher->download($photo->{'id'},$photo->{'urls'}->{'raw'});
					
					}
				}	
			});

			$response->on("end", function(){
				echo "Done\n"; 
			});

		});

		$request->on("error", function(\Exception $e){
			echo $e;
		});
		
		$request->end();
	
			
		$index++;
	}else{
		$index = 0;
	}	

});



$loop->run();

