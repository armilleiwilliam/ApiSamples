<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/*****************************************/
/****** Create a class which stores users
    management basic methods ************/
/****************************************/
class usersRecord {

    //Store token to be used in each request
    private $token = "";

    // store errors
    public $errors = array();

    // Guzzle client
    private $client = null;


    // create a token to make requests
    public function getToken(): string
    {
        global $token;
        $payload = [
                    "email" => "eve.holt@reqres.in",
                    "password" => "cityslicka"
            ];

        $token =  $this->basicConnection($payload, 'api/login', "");
        if(is_array($token) && count($token) > 0){
            $token = $token['token'];
            return $token;
        }
        throw new Exception ("Server communcation failure");
    }

    //Api connection
    public function basicConnection(array $payload, string $url, $token): array
    {
        global $client;
        try{
            // set guzzle client
            if($client == null){
                $client = new Client(['base_uri' => 'https://reqres.in/']);
            }

            if(null !== $token && $token !== ""){
                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ];
            } else {
                $headers = [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ];
            }

            //send post request
            $response = $client->post($url, [
                'form_params' => $payload,
                'headers' => $headers
            ]);

            return json_decode($response->getBody(), true);

        } catch(Exception $e){
            $errors["message"] = "Something went wrong: " . $e->getMessage();
        }

    }

    // Create user
    public function  createUser(string $name, string $job): string
    {
        global $token;

        //check if token has been set
        if($token == ""){
            $token = $this->getToken();
        }

        // user info
        $payload = [
                    "name" => $name,
                    "job" => $job
                ];
        $user = $this->basicConnection($payload, 'api/user', $token);

        if(is_array($user) && count($user) > 0){
            return "Name: " . $user["name"] . " Job: " . $user["job"] . " Id: " . $user["id"] . " Created:" . $user["createdAt"];
        }
        throw new Exception ("Something went wrong");
    }

    // Show all users
    public function  requestAllUsers(): array
    {
        global $token;
        global $client;

        // set guzzle client
        if($client == null){
            $client = new Client();
        }

        //check if token has been set
        if($token == ""){
            $token = $this->getToken();
        }

       $response = $client->get('https://reqres.in/api/users?page=2',
            [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]);

        return json_encode($response, true);
        //throw new Exception ("Something went wrong");
    }

    // Update user
    public function  updateUser(string $name, string $lastname, int $id): ?array
    {
        global $token;
        global $client;

        // set guzzle client
        if($client == null){
            $client = new Client();
        }

        //check if token has been set
        if($token == ""){
            $token = $this->getToken();
        }


        $payload = [
                "first_name" => $name,
                "last_name" => $lastname
            ];

       $request = new Request('PUT', 'https://reqres.in/api/users/' . $id,  [
            'form_params' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]]);

        $response = $client->send($request, ['timeout' => 2]);

        return json_encode($response, true);
    }

    //Delete user


    // Update user
    public function  deleteUser(int $id): ?array
    {
        global $token;
        global $client;

        // set guzzle client
        if($client == null){
            $client = new Client();
        }

        //check if token has been set
        if($token == ""){
            $token = $this->getToken();
        }

       $request = new Request('DELETE', 'https://reqres.in/api/users/' . $id,  [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]]);

        $response = $client->send($request, ['timeout' => 2]);

        return json_encode($response, true);
    }
}

?>