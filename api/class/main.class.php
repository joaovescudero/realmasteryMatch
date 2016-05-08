<?php

  class Main{

    /**
     * Riot API Key
     * @var String
     */
    var $APIKEY;

    /**
     * Limit of matches
     * @var Int
     */
    var $NUMMATCHES;

    /**
     * Base url
     * @var String
     */
    var $BASEURL;

    /**
     * Player region
     * @var String
     */
    var $REGION;

    /**
     * Player region
     * @var String
     */
    var $GLOBALURL;

    /**
     * Sending informations to class
     * @param  String $APIKEY     Riot API Key
     * @param  Int    $NUMMATCHES Limit of matches
     * @param  String $BASEURL    Base url
     * @param  String $REGION     Region
     */
    public function __construct($APIKEY, $NUMMATCHES, $BASEURL, $REGION, $GLOBALURL){
      $this->APIKEY = $APIKEY;
      $this->NUMMATCHES = $NUMMATCHES;
      $this->BASEURL = strtolower(sprintf($BASEURL, $REGION));
      $this->REGION = strtolower($REGION);
      $this->GLOBALURL = $GLOBALURL."".$APIKEY;
    }

    /**
     * Getting champ Id
     * @param  String $champKey Champ Key
     * @return Int              Champ Id
     */
    public function champId($champKey){
      //Connection
      $bridge = file_get_contents($this->GLOBALURL);
      //Decode json
      $json = json_decode($bridge, true);
      //Verifying if champKey exist
      if($json["data"][$champKey]["id"] == ""){$message['message'] = 'Champion key not found'; exit(json_encode($message));}
      //Returning champId
      return $json["data"][$champKey]["id"];
      
    }

    /**
     * Changing player username to user ID
     * @param  String $username player username
     * @return Int              player ID
     */
    public function playerUsername($username){
      //Transforming username
      $username = strtolower(str_replace(' ', '', $username));

      //Create an Url
      $url = $this->BASEURL.
             "/api/lol/".
             $this->REGION.
             "/v1.4/summoner/by-name/".
             $username.
             "?api_key=".
             $this->APIKEY;
      //Connect
      $bridge = file_get_contents($url);
      //Verify connection
      if(!$bridge){$message['message'] = 'Username not found'; exit(json_encode($message));}
      //Decode Json
      $json = json_decode($bridge, true);
      //Return User ID
      return $json[$username]['id'];
    }

    /**
     * Get play league
     * @param  Int $playerId Player id
     * @return Int           Coefficient multiplicator
     */
    public function playerLeague($playerId){
      //Creating an Url
      $url = $this->BASEURL.
             "/api/lol/".
             $this->REGION.
             "/v2.5/league/by-summoner/".
             $playerId.
             "?api_key=".
             $this->APIKEY;
      //Connect
      $bridge = file_get_contents($url);
      if(!$bridge){return 'UNRANKED';}
      //Decode Json
      $json = json_decode($bridge, true);
      //Getting User League
      $userLeague = $json[$playerId][0]["tier"];
      //Returning user league
      return $userLeague;
    }

    public function leagueCoefficient($league){
      //Setting Coefficient
      $coefficient = 1;
      //Getting new coefficient
      switch ($league) {
        case 'BRONZE':
          $coefficient = 1.0;
          break;
        case 'SILVER':
          $coefficient = 1.1;
          break;
        case 'GOLD':
          $coefficient = 1.25;
          break;
        case 'PLATINUM':
          $coefficient = 1.4;
          break;
        case 'DIAMOND':
          $coefficient = 1.5;
          break;
        case 'MASTER':
          $coefficient = 1.55;
          break;
        case 'CHALLENGER':
          $coefficient = 1.6;
          break;
        default:
          $coefficient = 1;
          break;
      }
      //Return coefficient
      return $coefficient;
    }

    /**
     * Getting player mastery
     * @param  Int $userId  User Id
     * @param  Int $champId Champion Id
     * @return Int          Champion mastery
     */
    public function playerMastery($userId, $champId){
      //Verifying region
      if($this->REGION == "br" || $this->REGION == "euw"){$region=$this->REGION."1";}elseif($this->REGION == "eune"){$region="eun1";}else{$region=$this->REGION;}
      //Creating an Url
      $url = $this->BASEURL.
             "/championmastery/location/".
             $region.
             "/player/".
             $userId.
             "/champion/".
             $champId.
             "?api_key=".
             $this->APIKEY;
      //Connection
      $bridge = file_get_contents($url);
      //Verifying if champion mastery exist
      if(!$bridge){$message['message'] = 'Champion id not found'; exit(0);}
      //Decode json
      $json = json_decode($bridge, true);
      //Returning champion mastery
      return $json['championLevel'];
    }

    /**
     * Get player matches
     * @param  Int $userId  User Id
     * @param  Int $champId Champion Id
     * @return Array          Matches Array
     */
    public function playerMatches($userId, $champId){
      //Creating an Url
      $url = $this->BASEURL.
             "/api/lol/".
             $this->REGION.
             "/v2.2/matchlist/by-summoner/".
             $userId.
             "?championIds=".
             $champId.
             "&beginIndex=0&endIndex=".
             $this->NUMMATCHES.
             "&api_key=".
             $this->APIKEY;
      //Connection
      $bridge = file_get_contents($url);
      //Verifying if matches exist
      if(!$bridge){$message['message'] = 'Matches not found'; exit(json_encode($message));}
      //Returning and decoding json
      return json_decode($bridge, true);
    }

    public function getMatch($matchId){
      //Creating an Url
      $url = $this->BASEURL.
             "/api/lol/".
             $this->REGION.
             "/v2.2/match/".
             $matchId.
             "?api_key=".
             $this->APIKEY;
      //Connection
      $bridge = file_get_contents($url);
      if(!$bridge){return $this->getMatch($matchId);}
      //Verifying connection
      if(!$bridge){return '';}
      //Decode json
      $json = json_decode($bridge, true);
      //Returning json
      return $json;
    }
  }
  