<?php

    /**
     * @author Arafat Benson 
     * @link https://github.com/fixture
     * 25th June, 2021 
     * 
     * 
     * To handle json response and group items according to their cateogory they belong 
     * In this example am using live football fixture for this date 25th June, 2021 
     * API used is from https://api-football-v1.p.rapidapi.com/v3/fixture endpoint in particular 
     * 
     * What brought about this issue what the fact that it was never documentated on how to used the api
     * and get fixture but this response should be grouped according to the leagues they belong to. But i 
     * was given the leagues for each fixture all i have to do was to find a way to group them by their leagues 
     */


    /**
     * I already downloaded the json response offline 
     * so that that i can work on it offline without making to many calls to the API
     * 
     */
    $response = file_get_contents("data.json");

    // decode the JSON string
	$json_response = json_decode($response, true);

    /**
     * for this we are working with the response array 
     * so we fetch the response array 
     */
    $fixture = $json_response['response'];


    /**
     * Loop through the array 
     * This is important becoz we are trying to re-modifiy the array 
     * and making the league id the primary index of the array 
     */
    for ( $i=0; $i < count($fixture); $i++ ) { 

        //fetch teams from the fixture
        $teams = $fixture[$i]['teams'];
        //fetch goals from the fixture
        $goals = $fixture[$i]['goals'];
        //fetch the score from the fixture 
        $score = $fixture[$i]['score'];

        //fetch the league Id
        $leaguesId = $fixture[$i]['league']['id'];
        //fetch the league details 
        $details = [
                "name" => $fixture[$i]['league']['name'],
                "country" => $fixture[$i]['league']['country'],
                "logo" => $fixture[$i]['league']['logo'],
                "flag" => $fixture[$i]['league']['flag'],
                "season" => $fixture[$i]['league']['season'],
        ];

        //store data in the new array called record
        $record['id'] = $leaguesId;
        $record['details'] = $details;
        $record['data'][0]['teams'] = $teams;
        $record['data'][0]['goals'] = $goals;
        $record['data'][0]['score'] = $score;

        //push into new array
        $leagues[] = $record;

    }

    //sort the new array by the id value
    $keys = array_column($leagues, 'id');
    array_multisort($keys, SORT_ASC, $leagues);

    echo '<pre>';
    
    /**
     * @param array $leagues
     * @param string id 
     * @return array $details
     * 
     * Call the fixture function to re-arrange the array by groups or leagues it belong to.
     */
    $details = fixture($leagues,'id');

    //print new array 
    print_r($details);

    /**
     * @param array $leagues
     * @param string id 
     * @return array $details
     */
    function fixture( $array, $key ){
        //new array to store the temp array 
        $temp_array = [];
        $i = 0;
        //new array to store the leagueId
        $leagueId = [];

        /**
         * loop through the array 
         * if league id do not exit in the leagueId array, add the id to the array 
         * else if exist 
         * search for the key of leagueId, add the match to the group 
         */
        foreach($array as $val) {
            //league Id exist in $leaguesId array
            if (!in_array($val[$key], $leagueId)) {
                //add id to the leagueId
                $leagueId[$i] = $val[$key];
                //add match the temp array 
                $temp_array[$i] = $val;
            }else{
                //search the key of the league id in the leagueId array
                $fixture_key = array_search($val[$key], $leagueId);
                //add the match the league group
                array_push( $temp_array[$fixture_key]['data'], $val["data"]);

            }
            $i++;
        }

        //return new array
        return $temp_array;
    }
  