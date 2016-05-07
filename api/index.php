<?php
  //
  header("Access-Control-Allow-Origin: *");

  //Including config file
  require_once("config/config.php");
  //Including main class file
  require_once("class/main.class.php");

  //Getting region by GET
  $REGION = $_GET["region"];

  //Getting username by GET
  $USERNAME = $_GET["username"];

  //Starting main class
  $main = new Main($APIKEY, $NBRMATCHES, $BASEURL, $REGION, $GLOBALURL);

  //Getting Champ Id
  if(isset($_GET["champkey"])){
    $champId = $main->champId($_GET["champkey"]);
  }elseif(isset($_GET["champid"])){
    $champId = $_GET["champid"];
  }

  //Getting User ID
  $userId = $main->playerUsername($USERNAME);

  //Getting User League
  $userLeague = $main->playerLeague($userId);
  $coefficient = $main->leagueCoefficient($userLeague);

  //Getting User champId mastery
  $champMastery = $main->playerMastery($userId, $champId);

  //Getting User matches
  $userMatches = ($main->playerMatches($userId, $champId));

  //Setting global arrays
  //Error messages
  $message = array('message' => '');
  //User Lane
  $lane = array();
  //User Role
  $role = array();
  //Global user stats
  $statsGlobal = array('nPartidas' => 0, 'nPartidasGanhas' => 0, 'nMaestria' => 0);
  //Global user points
  $points = array('KDA' => 0, 'Gold' => 0, 'WardsPlaced' => 0, 'KillingSpree' => 0, "DoubleKill" => 0, "TripleKill" => 0, "QuadraKill" => 0, "PentaKill" => 0, 'CreepEarly' => 0, 'CreepMid' => 0, 'CreepLate' => 0, 'NeutralCreep' => 0, 'TotalDamageDealt' => 0, 'TotalDamageTaken' => 0);
  //Setting user mastery
  $statsGlobal["nMaestria"] = $champMastery;

  for($i = 0; $i < $NBRMATCHES; $i++){
    //Setting local stats array
    $stats = array('Role' => '', 'Lane' => '');

    //Verifying of matchId exist
    if(!isset($userMatches['matches'][$i]['matchId'])){
      break;
    }

    $matchId = $userMatches['matches'][$i]['matchId'];

    if(!empty($matchId)){
      $jsonNMatch = $main->getMatch($matchId);
    }
    
    if(isset($jsonNMatch)){
      //Getting participant Id
      $participantId = null;
      for($z=0;$z<=9;$z++){
        if($jsonNMatch['participantIdentities'][$z]["player"]["summonerId"] == $userId){
          $participantId = $jsonNMatch['participantIdentities'][$z]['participantId'];
        }
      }

      $participantId = $participantId - 1;

      //Getting number of matches and number of wins
      if($jsonNMatch['participants'][$participantId]["stats"]["winner"]){
        $statsGlobal['nPartidas'] += 1;
        $statsGlobal['nPartidasGanhas'] += 1;
      }else{
        $statsGlobal['nPartidas'] += 1;
      }

      //Getting user KDA
      $kda = $jsonNMatch['participants'][$participantId]["stats"]["kills"].'/'.$jsonNMatch['participants'][$participantId]["stats"]["deaths"].'/'.$jsonNMatch['participants'][$participantId]["stats"]["assists"];

      if($jsonNMatch['participants'][$participantId]["stats"]["deaths"] == 0){
        $jsonNMatch['participants'][$participantId]["stats"]["deaths"] = 1;
      }

      //Getting user KDA Ratio
      $kdaRatio = ($jsonNMatch['participants'][$participantId]["stats"]["kills"] + $jsonNMatch['participants'][$participantId]["stats"]["assists"]) / $jsonNMatch['participants'][$participantId]["stats"]["deaths"];

      if($kdaRatio > 0){
        $points["KDA"] = $kdaRatio + $points["KDA"];
      }

      //Getting user gold
      $points["Gold"] += $jsonNMatch['participants'][$participantId]["stats"]["goldEarned"];
      //Getting user ward placed
      $points["WardsPlaced"] += $jsonNMatch['participants'][$participantId]["stats"]["wardsPlaced"];
      //Getting user killingSpree
      $points["KillingSpree"] += $jsonNMatch['participants'][$participantId]["stats"]["largestKillingSpree"];
      //Getting user Multikills
      $points["DoubleKill"] += $jsonNMatch['participants'][$participantId]["stats"]["doubleKills"];
      $points["TripleKill"] += $jsonNMatch['participants'][$participantId]["stats"]["tripleKills"];
      $points["QuadraKill"] += $jsonNMatch['participants'][$participantId]["stats"]["quadraKills"];
      $points["PentaKill"] += $jsonNMatch['participants'][$participantId]["stats"]["pentaKills"];
      //Getting user CS Early
      $points["CreepEarly"] += $jsonNMatch['participants'][$participantId]["timeline"]["creepsPerMinDeltas"]["zeroToTen"];
      //Getting and verifying user CS Middle
      if(!empty($jsonNMatch['participants'][$participantId]["timeline"]["creepsPerMinDeltas"]["tenToTwenty"])){
        $points["CreepMid"] += $jsonNMatch['participants'][$participantId]["timeline"]["creepsPerMinDeltas"]["tenToTwenty"];
      }
      //Getting and verifying user CS Late
      if(!empty($jsonNMatch['participants'][$participantId]["timeline"]["creepsPerMinDeltas"]["twentyToThirty"])){
        $points["CreepLate"] += $jsonNMatch['participants'][$participantId]["timeline"]["creepsPerMinDeltas"]["twentyToThirty"];
      }
      //Getting user neutral CS
      $points["NeutralCreep"] += $jsonNMatch['participants'][$participantId]["stats"]["neutralMinionsKilled"];
      //Getting user total damage dealt
      $points["TotalDamageDealt"] += $jsonNMatch['participants'][$participantId]["stats"]["totalDamageDealt"];
      //Getting user total damage taken
      $points["TotalDamageTaken"] += $jsonNMatch['participants'][$participantId]["stats"]["totalDamageTaken"];
      //Getting user role
      $stats["Role"] = $userMatches['matches'][$i]["role"];
      //Getting user lane
      $stats["Lane"] = $userMatches['matches'][$i]["lane"];

      array_push($role, $userMatches['matches'][$i]["role"]);
      array_push($lane, $userMatches['matches'][$i]["lane"]);
    }
  }
  //Setting user role and lane
  $role = array_count_values($role);
  arsort($role);
  $role = key($role);
  $lane = array_count_values($lane);
  arsort($lane);
  $lane = key($lane);

  //Correcting user points
  $points["KDA"] = $points["KDA"] / $statsGlobal["nPartidas"];
  $points["Gold"] = ($points["Gold"] / 1000) / $statsGlobal["nPartidas"];
  $points["WardsPlaced"] = $points["WardsPlaced"] / $statsGlobal["nPartidas"];
  $points["KillingSpree"] = $points["KillingSpree"] / $statsGlobal["nPartidas"];
  $points["DoubleKill"] = $points["DoubleKill"] / $statsGlobal["nPartidas"];
  $points["TripleKill"] = $points["TripleKill"] / $statsGlobal["nPartidas"];
  $points["QuadraKill"] = $points["QuadraKill"] / $statsGlobal["nPartidas"];
  $points["PentaKill"] = $points["PentaKill"] / $statsGlobal["nPartidas"];
  $points["CreepEarly"] = $points["CreepEarly"] / $statsGlobal["nPartidas"];
  $points["CreepMid"] = $points["CreepMid"] / $statsGlobal["nPartidas"];
  $points["CreepLate"] = $points["CreepLate"] / $statsGlobal["nPartidas"];
  $points["NeutralCreep"] = $points["NeutralCreep"] / $statsGlobal["nPartidas"];
  $points["TotalDamageDealt"] = ($points["TotalDamageDealt"] / 10000) / $statsGlobal["nPartidas"];
  $points["TotalDamageTaken"] = ($points["TotalDamageTaken"] / 10000) / $statsGlobal["nPartidas"];
  $points["nPartidas"] = $statsGlobal['nPartidas'];
  $points["nPartidasGanhas"] = ($statsGlobal["nPartidasGanhas"] / $statsGlobal["nPartidas"]) * 10;
  $points["nMaestria"] = $statsGlobal["nMaestria"];
  $points["league"] = $coefficient;

  //Changing coefficient for certain roles
  $coefficientDamageTaken = 3;
  $coefficientDamageDelt = 3;
  $coefficientNeutralCreep = 3;
  $coefficientWardsPlaced = 3;
  
  if(($lane == "MID" || $lane == "MIDDLE") || (($lane == "BOT" || $lane == "BOTTOM") & $role == "DUO_CARRY")){
    $coefficientDamageDelt = 7;
  }
  if($lane == "TOP"){
    $coefficientDamageTaken = 9;
    $coefficientDamageDelt = 7;
  }
  if($lane == "JUNGLE"){
    $coefficientNeutralCreep = 7;
  }
  if(($lane == "BOT" || $lane == "BOTTOM") & $role == "DUO_SUPPORT"){
    $coefficientWardsPlaced = 5;
  }

  //Running our points equation
  $equation = (
      ($points["CreepEarly"] * 5)
    + (($points["nPartidasGanhas"] * 10) * $coefficient)
    + (($points["KDA"] * 9) * $coefficient)
    + (($points["PentaKill"] * 9) * $coefficient)
    + (($points["QuadraKill"] * 7) * $coefficient)
    + (($points["KillingSpree"] * 8) * $coefficient)
    + ($points["CreepMid"] * 3) 
    + (($points["TripleKill"] * 5) * $coefficient)
    + (($points["DoubleKill"] * 3) * $coefficient)
    + ($points["WardsPlaced"] * $coefficientWardsPlaced)
    + (($points["TotalDamageTaken"] * $coefficientDamageTaken) * $coefficient)
    + (($points["TotalDamageDealt"] * $coefficientDamageDelt) * $coefficient)
    + ($points["NeutralCreep"] * $coefficientNeutralCreep)
    + ($points["nMaestria"] * 5)
    + ($points["CreepLate"] * 2) 
    + ($points["Gold"] * 3)
    )
    / //divided by
    (
      $points["CreepEarly"] 
    + $points["CreepMid"]
    + $points["CreepLate"]
    + $points["nPartidasGanhas"]
    + $points["KDA"]
    + $points["PentaKill"]
    + $points["QuadraKill"]
    + $points["KillingSpree"]
    + $points["CreepMid"]
    + $points["TripleKill"]
    + $points["DoubleKill"]
    + $points["WardsPlaced"]
    + $points["TotalDamageTaken"]
    + $points["TotalDamageDealt"]
    + $points["NeutralCreep"]
    + $points["nMaestria"]
    + $points["CreepLate"]
    + $points["Gold"]
  );

  //Rounding result
  $equation = round(($equation * (1 - (1/$points["nPartidas"])))*100);

  //Showing and encoding result
  $result = array( 'points' => $equation, 'stats' => $points, 'user' => $USERNAME);
  echo json_encode($result);