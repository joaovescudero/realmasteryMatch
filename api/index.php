<?php
  //
  header("Access-Control-Allow-Origin: http://localhost:8080/");

  //Including config file
  require_once("config/config.php");
  //Including main class file
  require_once("class/main.class.php");

  //Getting region by GET
  $REGION = $_GET["region"];

  //Getting username by GET
  $USERNAME = $_GET["username"];

  //Starting main class
  $main = new Main($APIKEY, $NUMMATCHES, $BASEURL, $REGION, $GLOBALURL);

  //Getting Champ Id
  if(isset($_GET["champkey"])){
    $champId = $main->champId($_GET["champkey"]);
  }elseif(isset($_GET["champid"])){
    $champId = $_GET["champid"];
  }

  //Getting User ID
  $userId = $main->getPlayerUsername($USERNAME);

  //Getting User League
  $userLeague = $main->getPlayerLeague($userId);
  $LC = $main->getLeagueCoefficient($userLeague);

  //Getting User champId mastery
  $champMastery = $main->getPlayerMastery($userId, $champId);

  //Getting User matches
  $userMatches = ($main->getPlayerMatches($userId, $champId));

  //Setting global arrays
  //Error messages
  $message = array('message' => '');
  //User Lane
  $lane = array();
  //User Role
  $role = array();
  //Global user stats
  $statsGlobal = array('nMatches' => 0, 'nMatchesWon' => 0, 'MasteryLevel' => 0);
  //Global user points
  $points = array('KDA' => 0, 'Gold' => 0, 'WardsPlaced' => 0, 'KillingSpree' => 0, "DoubleKill" => 0, "TripleKill" => 0, "QuadraKill" => 0, "PentaKill" => 0, 'CreepEarly' => 0, 'CreepMid' => 0, 'CreepLate' => 0, 'NeutralCreeps' => 0, 'TotalDamageDealt' => 0, 'TotalDamageTaken' => 0);
  //Setting user mastery
  $statsGlobal["MasteryLevel"] = $champMastery;

  for($i = 0; $i < $NUMMATCHES; $i++){
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
        $statsGlobal['nMatches'] += 1;
        $statsGlobal['nMatchesWon'] += 1;
      }else{
        $statsGlobal['nMatches'] += 1;
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
      $points["NeutralCreeps"] += $jsonNMatch['participants'][$participantId]["stats"]["neutralMinionsKilled"];
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
  $points["KDA"] = $points["KDA"] / $statsGlobal["nMatches"];
  $points["Gold"] = ($points["Gold"] / 1000) / $statsGlobal["nMatches"];
  $points["WardsPlaced"] = $points["WardsPlaced"] / $statsGlobal["nMatches"];
  $points["KillingSpree"] = $points["KillingSpree"] / $statsGlobal["nMatches"];
  $points["DoubleKill"] = $points["DoubleKill"] / $statsGlobal["nMatches"];
  $points["TripleKill"] = $points["TripleKill"] / $statsGlobal["nMatches"];
  $points["QuadraKill"] = $points["QuadraKill"] / $statsGlobal["nMatches"];
  $points["PentaKill"] = $points["PentaKill"] / $statsGlobal["nMatches"];
  $points["CreepEarly"] = $points["CreepEarly"] / $statsGlobal["nMatches"];
  $points["CreepMid"] = $points["CreepMid"] / $statsGlobal["nMatches"];
  $points["CreepLate"] = $points["CreepLate"] / $statsGlobal["nMatches"];
  $points["NeutralCreeps"] = $points["NeutralCreeps"] / $statsGlobal["nMatches"];
  $points["TotalDamageDealt"] = ($points["TotalDamageDealt"] / 10000) / $statsGlobal["nMatches"];
  $points["TotalDamageTaken"] = ($points["TotalDamageTaken"] / 10000) / $statsGlobal["nMatches"];
  $points["nMatches"] = $statsGlobal['nMatches'];
  $points["nMatchesWon"] = ($statsGlobal["nMatchesWon"] / $statsGlobal["nMatches"]) * 10;
  $points["MasteryLevel"] = $statsGlobal["MasteryLevel"];
  $points["league"] = $LC;

  //Changing coefficient for certain roles
  $RCDamageTaken = 3;
  $RCDamageDelt = 3;
  $RCNeutralCreeps = 3;
  $RCWardsPlaced = 3;
  
  if(($lane == "MID" || $lane == "MIDDLE") || (($lane == "BOT" || $lane == "BOTTOM") & $role == "DUO_CARRY")){
    $RCDamageDelt = 7;
  }
  if($lane == "TOP"){
    $RCDamageTaken = 9;
    $RCDamageDelt = 7;
  }
  if($lane == "JUNGLE"){
    $RCNeutralCreeps = 7;
  }
  if(($lane == "BOT" || $lane == "BOTTOM") & $role == "DUO_SUPPORT"){
    $RCWardsPlaced = 5;
  }

  //Running our points equation
  $equation = (
      ($points["CreepEarly"] * 5)
    + (($points["nMatchesWon"] * 10) * $LC)
    + (($points["KDA"] * 9) * $LC)
    + (($points["PentaKill"] * 9) * $LC)
    + (($points["QuadraKill"] * 7) * $LC)
    + (($points["KillingSpree"] * 8) * $LC)
    + ($points["CreepMid"] * 3) 
    + (($points["TripleKill"] * 5) * $LC)
    + (($points["DoubleKill"] * 3) * $LC)
    + ($points["WardsPlaced"] * $RCWardsPlaced)
    + (($points["TotalDamageTaken"] * $RCDamageTaken) * $LC)
    + (($points["TotalDamageDealt"] * $RCDamageDelt) * $LC)
    + ($points["NeutralCreeps"] * $RCNeutralCreeps)
    + ($points["MasteryLevel"] * 5)
    + ($points["CreepLate"] * 2) 
    + ($points["Gold"] * 3)
    )
    / //divided by
    (
      $points["CreepEarly"] 
    + $points["CreepMid"]
    + $points["CreepLate"]
    + $points["nMatchesWon"]
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
    + $points["NeutralCreeps"]
    + $points["MasteryLevel"]
    + $points["CreepLate"]
    + $points["Gold"]
  );

  //Rounding result
  $equation = round(($equation * (1 - (1/$points["nMatches"])))*100);

  //Showing and encoding result
  $result = array( 'points' => $equation, 'stats' => $points, 'user' => $USERNAME);
  echo json_encode($result);
  
