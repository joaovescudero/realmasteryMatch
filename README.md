RealMastery Match
============
[![GitHub Stars](https://img.shields.io/github/stars/joaovescudero/realmasteryMatch.svg)](https://github.com/joaovescudero/realmasteryMatch/stargazers) [![GitHub Issues](https://img.shields.io/github/issues/joaovescudero/realmasteryMatch.svg)](https://github.com/joaovescudero/realmasteryMatch/issues) [![Current Version](https://img.shields.io/badge/version-0.1-green.svg)](https://github.com/joaovescudero/realmasteryMatch/) [![Live Demo](https://img.shields.io/badge/demo-online-green.svg)](http://joaovescudero.me:8080/riot)

* [Intro](#intro)
* [Demo](#demo)
* [Getting started](#start)
* [F.A.Q](#faq)
* [Contact](#contact)
* [License](#license)

## Live Demo
http://joaovescudero.me:8080/riot

## Overview
Fight against your friends and whoever you want. With algoritims and a lot of data, we can calculate how well summoners have played in the last 5 rankeds with a specific champion.

## RealMastery Index
We created RealMastery: a Player Effiency Rating for LoL based on the last 5 ranked games of a summoner with a specific champion.
This project uses the RIOT API to get the data from these matches and calculate a Player Effiency Rating by a **Weighted average**:

```javascript
(((CreepEarly * 5) + (CreepMid * 3) + (CreepLate * 2) + (Gold * 3) + (nMatchesWon * 10) * LC) + (((Kills+Assists)/Deaths) * 9) * LC) + ((PentaKills * 9) * LC) + ((QuadraKills * 7) * LC) + ((TripleKills * 5) * LC) + ((DoubleKills * 3) * LC) + ((MaxKillingSpree * 8) * LC) + ((TotalDamageDealt * RC) * LC) + ((TotalDamageTaken * RC) * LC) + (WardsPlaced * RC) + (NeutralCreeps * RC) + (MasteryLevel * 5) )
/
(CreepEarly + CreepMid + CreepLate + Gold + nMatchesWon + ((Kills+Assists)/Deaths)) + PentaKills + QuadraKills + TripleKills + DoubleKills + MaxKillingSpree + TotalDamageDealt + TotalDamageTaken + WardsPlaced + NeutralCreeps + MasteryLevel))) * (1 - (1/nMatches)) * 100
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

##Technology Stack
###API Processor
- PHP

We decided to use PHP because it's more simpler to install in machines and to contribute, a very complete language and have a lot of support in web servers.

####Config:
 - **APIKEY**: Your RIOT API Key
 - **NBRMATCHES**: Number of matches you want to analyze

####Functions:
- **getChampionId**: Gets champion id by champion name (champKey) (Optional)
- **playerUsename**: Get the summoner id by username
- **playerLeague**: Get the summoner league by user id(Just leagues, not division)
- **leagueCoefficient**: Transform league name in league coefficient
- **playerMastery**: Get summoner champion mastery with a championId
- **playerMatches**: Get summoner's ranked matches id
- **getMatch**: Get information from a specific match by match id

####URL Request Example:
http://joaovescudero.me:8080/riot/api/?region=br&username=HKZ%20BrushyMan&champid=412
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
  - Facebook SDK
  - Titillium Web font from Google Fonts

####URL Match Request Example:
http://joaovescudero.me:8080/riot/#br-HKZ%20BrushyMan-412!vs!br-HKZ%20soloT-54

Hash data order:
 1. Player Region
 2. Player Username
 3. Player Champion Id
