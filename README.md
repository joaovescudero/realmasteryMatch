# RealMastery Match

### Live Demo
http://joaovescudero.me:8080/riot

## Overview
Fight against your friends and whoever you want. With algoritims and a lot of data, we can calculate how well summoners have played in the last 5 rankeds with a specific champion.

## RealMastery Index
We created RealMastery: a Player Effiency Rating for LoL based on the last 5 ranked games of a summoner with a specific champion.
This project uses the RIOT API to get the data from these matches and calculate a Player Effiency Rating by a **Weighted average**:

`(((CreepEarly * 5) + (CreepMid * 3) + + (CreepLate * 2) + (Gold * 3) + (nMatchesWon * 10) * LC) + (((Kills+Assists)/Deaths) * 9) * LC) + ((PentaKills * 9) * LC) + ((QuadraKills * 7) * LC) + ((TripleKills * 5) * LC) + ((DoubleKills * 3) * LC) + ((MaxKillingSpree * 8) * LC) + ((TotalDamageDealt * SC) * LC) + ((TotalDamageTaken * SC) * LC) + (WardsPlaced * SC) + (NeutralCreeps * LC) + (MasteryLevel * 5) )/
(CreepEarly + CreepMid + CreepLate + Gold + nMatchesWon + ((Kills+Assists)/Deaths)) + PentaKills + QuadraKills + TripleKills + DoubleKills + MaxKillingSpree + TotalDamageDealt + TotalDamageTaken + WardsPlaced + NeutralCreeps + MasteryLevel))} * (1 - (1/nMatches)) * 100`

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
- SC: Special Coefficient.
  - For mid laners and adcarries, TotalDamageDealt has a SC of 7;
  - For top laners, TotalDamageDealt has a SC of 7 and a TotalDamageTaken of 9;
  - For junglers, NeutralCreeps has a SC of 7;
  - For supports, WardsPlaced has a SC of 5;
  - Default SC value is 3

###Why weighted average, league and special coefficients?
Weighted average is used in a lot of cases and can do pretty good calculating player efficiency rating. Since we (developers) are players of League of Legends, we weighted all the data from a match, but it wasn't fair.
Supports don't have a high Creep, mid laners and adcarries should have priorities on Total Damage Dealt, top laners should receive benefits from tanking and junglers from neutral creeps, so, we decided to create special coefficients to supply this differences betweeen roles.
League coefficients supports the idea that a KDA Ratio of 5 in a Challenge League is much more difficult than in a Bronze League.

##Technology Stack
###API Processor
- PHP

Why PHP?

Config:
 - **APIKEY**: Describe config
 - **NBRMATCHES**: Describe config

Classes:
- **playerUsename**: Describe class
- **playerLeague**: Describe class
- **leagueCoefficient**: Describe class
- **playerMastery**: Describe class
- **playerMatches**: Describe class
- **getMatch****: Describe class

###Web Application
- Languages:
 - Javascript
 - HTML5
 - CSS3

- Hosting:
  - DigitalOcean

- Packages:
  - jQuery
  - Facebook SDK
