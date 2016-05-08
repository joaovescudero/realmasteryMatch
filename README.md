RealMastery Match
============
[![GitHub Stars](https://img.shields.io/github/stars/joaovescudero/realmasteryMatch.svg)](https://github.com/joaovescudero/realmasteryMatch/stargazers) [![GitHub Issues](https://img.shields.io/github/issues/joaovescudero/realmasteryMatch.svg)](https://github.com/joaovescudero/realmasteryMatch/issues) [![Current Version](https://img.shields.io/badge/version-0.1-green.svg)](https://github.com/joaovescudero/realmasteryMatch/) [![Live Demo](https://img.shields.io/badge/demo-online-green.svg)](http://joaovescudero.me:8080/riot)

* [Demo](#demo)
* [Overview](#overview)
* [Getting Started](#start)
* [RealMastery Index](#rmindex)
* [Technology Stack](#stack)

##<a name="demo"></a> Demo
http://joaovescudero.me:8080/riot

##<a name="overview"></a> Overview
This project uses the RIOT API to get data of a player's last 5 ranked matches with a specific champion and calculates the RealMastery Index, a Player Efficiency Rating created by us, and compares it with others. It allows you to fight against your friends and whoever you want with algorithms and a lot of data.

## <a name="start"></a> Getting started

#### Setup the project

* Install PHP and run it

* Clone the project in your www folder (php)

        $ git clone https://github.com/joaovescudero/realmasteryMatch.git

* Enter project folder

        $ cd realmasteryMatch

* Set your API Key in api/config/config.php

* Go to http://localhost/realmasteryMatch


##<a name="rmindex"></a> RealMastery Index
We created RealMastery: a Player Efficiency Rating for League of Legends based on the last 5 ranked games of a summoner with a specific champion.
This project uses the RIOT API to get the data from these matches and calculate a Player Efficiency Rating by a weighted average:

```javascript
(((CreepEarly * 5) + (CreepMid * 3) + (CreepLate * 2) + (Gold * 3) + (nMatchesWon * 10) * LC) + (((Kills+Assists)/Deaths) * 9) * LC) + ((PentaKills * 9) * LC) + ((QuadraKills * 7) * LC) + ((TripleKills * 5) * LC) + ((DoubleKills * 3) * LC) + ((MaxKillingSpree * 8) * LC) + ((TotalDamageDealt * RC) * LC) + ((TotalDamageTaken * RC) * LC) + (WardsPlaced * RC) + (NeutralCreeps * RC) + (MasteryLevel * 5) )
/
(CreepEarly + CreepMid + CreepLate + Gold + nMatchesWon + ((Kills+Assists)/Deaths)) + PentaKills + QuadraKills + TripleKills + DoubleKills + MaxKillingSpree + TotalDamageDealt + TotalDamageTaken + WardsPlaced + NeutralCreeps + MasteryLevel)))
* (1 - (1/nMatches)) * 100
```

**Important: all of the vars below is acummulated and divided by the number of matches played with the champion (max: 5)**
- CreepEarly: Creeps per minute (0 to 10 minutes) ~ Weight: 5
- CreepMid: Creeps per minute (10 to 20 minutes) ~ Weight: 3
- CreepLate: Creeps per minute (20 minutes until end) ~ Weight: 2
- Gold: (Total of gold earned/1000) ~ Weight: 3 
- nMatchesWon: (nMatchesWon / nMatches) * 10 ~ Weight: 10
- KDA Ratio: (Kills+Assists)/Deaths ~ Weight: 9
- PentaKills ~ Weight: 9
- QuadraKills ~ Weight: 7
- TripleKills ~ Weight: 5
- DoubleKills ~ Weight: 3
- MaxKillingSpree ~ Weight: 8
- TotalDamageDealt: TotalDamageDealt / 10000 ~ Weight: Variable (Default: 3)
- TotalDamageTaken: TotalDamageTaken / 10000 ~ Weight: Variable (Default: 3)
- WardsPlaced ~ Weight: Variable (Default: 3)
- NeutralCreeps ~ Weight: Variable (Default: 3)
- MasteryLevel ~ Weight: 5
- LC: League Coefficient.
  - Bronze: 1.0;
  - Silver: 1.1;
  - Gold: 1.25;
  - Platinum: 1.4;
  - Diamond: 1.5;
  - Master: 1.55;
  - Challenger: 1.6
- RC: Role Coefficient.
  - For mid laners and adcarries, TotalDamageDealt has a RC of 7;
  - For top laners, TotalDamageDealt has a RC of 7 and a TotalDamageTaken of 9;
  - For junglers, NeutralCreeps has a RC of 7;
  - For supports, WardsPlaced has a RC of 5;
  - Default RC value is 3

####Creeps weight
Creeps weight are based on the importance and difficulty of the gold in its time, assuming that in the first 10 minutes of game the gold can do more advantage than later, thus, skills are less powerfull, causing farm to be harder.

####League coefficient
Datas are different from league to league, in high elos is more difficult to get high numbers than lowers elos. We did League Coefficients to solve this problem by fixed values that are applied just in some stats: nMatchesWon, KDA ratio, all multikills, maxKillingSpree, and the damage ones, so creeps and other stats dont differ. This can do great difference on the final result and it's more fair. 

####Role coefficient
Each role have its main characteristics. Comparing different players in different roles and with different champions is something very hard and cause a lot of discussions. Since the RIOT API provides datas about which role the summoner played in a specific game, we use this data to balance all those differences: wardsPlaced is valuable for support, so does totalDamageDealt for mid laners, top laners and adcarries, also neutralCreeps for junglers (they dont have high creep stats).

##<a name="stack"></a>Technology Stack
###API Processor
* PHP

We decided to use PHP because of its simplicity to install, the server support and the facility to everyone contribute, besides its power.


> "Written in PHP so literally anyone can contribute, even if they have no idea how to program. Even babies and dogs can contribute. You, too, can contribute!" - Phabricator.org


####Config:
 - **APIKEY**: Your RIOT API Key
 - **NUMMATCHES**: Number of matches you want to analyze (Recommended: 5)

####Functions:
- **getChampionId**: Gets champion id by champion name (champKey) (Optional)
- **getPlayerUsename**: Gets the summoner id by username
- **getPlayerLeague**: Gets the summoner league by summoner id (just leagues, not division)
- **getPlayerMastery**: Gets summoner's champion mastery
- **getPlayerMatches**: Gets summoner's ranked matches id
- **getMatch**: Gets data from a specific match by match id
- **getLeagueCoefficient**: Gets summoner's league coefficient

####URL Request Example:
`http://joaovescudero.me:8080/riot/api/?region=br&username=HKZ%20BrushyMan&champid=412`

*Response in JSON*

 - **region**: the user region
 - **username**: the user username (can contain spaces)
 - **champId**: the champion id (or champKey)
 - **champKey**: the champion name (or champId)

=============
###Web Application
####Languages:
 - Javascript
 - HTML5
 - CSS3

####Hosting:
  - DigitalOcean

####Packages:
  - jQuery
  - Angular
  - Facebook SDK
  - Titillium Web font from Google Fonts

####URL Match Request Example:
`http://joaovescudero.me:8080/riot/#br-HKZ%20BrushyMan-412!vs!br-HKZ%20soloT-54`

Hash data order:
 1. Player Region
 2. Player Username
 3. Player Champion Id


####Functions:
- **round**: Round numbers in maximum 2 decimal values
- **clearChampionSelect**: Remove the style of a champion selected and turns each one in black and white
- **getUserLeague**: Transform the LC provided by API in words (*Example: 1.4 to Platinum*, it's used in the match results table)
- **doMatch**: Hide a lot elements, do AJAX requests to API, prepare the match results table with rounded numbers and include Facebook Share
- **resetMatch**: Hide a lot of elements and backs to the first step (Player 1 information)
