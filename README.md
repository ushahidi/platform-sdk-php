# platform-sdk-php

## Setup notes

- Setup the following repository in your composer.json
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ushahidi/platform-sdk-php"
        }
    ]
```
- Install using `composer require ushahidi/platform-sdk-php:dev-master`


## Usage

#### Get list of available surveys 

```
$client = new \PlatformSDK\Ushahidi('http://192.168.33.110', '5');
$result = $client->getAvailableSurveys()

// $result will be an associative array with the response
```


#### Get a specific survey, fully hydrated with all required entities
```
$client = new \PlatformSDK\Ushahidi('http://192.168.33.110', '5');
// $id is the survey ID which you can get from the list of available surveys
 
$result = $client->getSurvey($id);

// $result will be an associative array with the response

```
#### Create a new Post

```
$client = new \PlatformSDK\Ushahidi('http://192.168.33.110', '5');
$result = $client->createPost($array)

// $result will be an associative array with the response
```

