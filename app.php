<?php

use Noodlehaus\Config;
use Noodlehaus\Parser\Json;
use React\HttpClient\Client;
use React\HttpClient\Response;


require __DIR__ . "/vendor/autoload.php";

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

$loop->addPeriodicTimer(3, function() use ($client,$url,&$headers,$categories, &$index){
	
	if($index < count($categories)){

		echo "Cat: " . $categories[$index] . "\n";

		$request = $client->request("GET", $url . "search/photos?query=" . $categories[$index], $headers);
	
		$request->on("response", function($response){
			
			$response->on("data", function($chunk){
				$json = json_decode($chunk);
				foreach($json->results as $photo){
					echo $photo->urls->raw . "\n";
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

