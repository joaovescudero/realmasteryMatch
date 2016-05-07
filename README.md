# RealMastery Match

### Live Demo
http://joaovescudero.me:8080/riot

## Overview
RealMastery is a Player Effiency Rating for LoL based on the last 5 ranked games of a summoner with a specific champion.
This project uses the RIOT API to get the data from these matchs and calculate a Player Effiency Rating by a **Weighted average**:

`(((CreepEarly * 5) + (CreepMid * 3) + + (CreepLate * 2) + (Gold * 3) + (nMatchesWon * 10) * LC) + (((Kills+Assists)/Deaths) * 9) * LC) + ((PentaKills * 9) * LC) + ((QuadraKills * 7) * LC) + ((TripleKills * 5) * LC) + ((DoubleKills * 3) * LC) + ((MaxKillingSpree * 8) * LC) + ((TotalDamageDealt * SC) * LC) + ((TotalDamageTaken * SC) * LC) + (WardsPlaced * SC) + (NeutralCreeps * LC) + (MasteryLevel * 5) )/
(CreepEarly + CreepMid + CreepLate + Gold + nMatchesWon + ((Kills+Assists)/Deaths)) + PentaKills + QuadraKills + TripleKills + DoubleKills + MaxKillingSpree + TotalDamageDealt + TotalDamageTaken + WardsPlaced + NeutralCreeps + MasteryLevel))} * (1 - (1/nMatches)) * 100`

**Important: all of the vars below is divided by the number of matches played with the champion (max: 5)**
- CreepEarly: Creeps per minute (0 to 10 minutes)
- CreepMid: Creeps per minute (10 to 20 minutes)
- CreepLate: Creeps per minute (20 minutes until end)
- Gold: (Total of gold earned/1000)
- nMatchesWon: (nMatchesWon / nMatches) * 10
- TotalDamageDealt: TotalDamageDealt / 10000
- TotalDamageTaken: TotalDamageTaken / 10000
- LC: League Coefficient.
  - Bronze: 1.0;
  - Silver: 1.1;
  - Gold: 1.25;
  - Platinum: 1.4;
  - Diamond: 1.5;
  - Master: 1.55;
  - Challenger: 1.6;
- SC: Special Coefficient.
  - For mid laners and adcarries, TotalDamageDealt has a SC of 7;
  - For top laners, TotalDamageDealt has a SC of 7 and a TotalDamageTaken of 9;
  - For junglers, NeutralCreeps has a SC of 7;
  - For supports, WardsPlaced has a SC of 5;
  - Default SCs:
    - TotalDamageTaken: 3;
    - TotalDamageDealt: 3;
    - NeutralCreeps: 3;
    - WardsPlaced: 3;
    
