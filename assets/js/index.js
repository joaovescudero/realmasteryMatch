$(document).ready(function(){
	/* Initializing vars */
	var match = {
		region: [],
		user: [],
		champion: [],
		championName: [],
		results: []
	};
	var step = 0;
	var p0 = [];
	var p1 = [];
	var stats = ["KDA", "Gold", "WardsPlaced", "KillingSpree", "DoubleKill", "TripleKill", "QuadraKill", "PentaKill", "CreepEarly", "CreepMid", "CreepLate", "NeutralCreeps", "TotalDamageDealt", "TotalDamageTaken", "nMatchesWon", "MasteryLevel", "league"];
	var apiUrl = 'https://joaovescudero.me/riot/api/';

	/* Hide things at start */
	$('#results-table').hide();
	$('#loading').hide();
	$('#match-wrapper').hide();

	/*
		Round numbers in 2 decimal places
		Used in: Match Results Table
		Why: Facilitate the reading
		@param Number value 	The number to round
		@param Number decimals 	The number of decimals of the final number
	*/
	function round(value, decimals) {
    	return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
	}

	/*
		Remove the style of a champion selected and turns each one in black and white
		Used in: Champion Select
		Why: Enhance the User Experience by notability of which champion the user is selecting
	*/
	function clearChampionSelect() {
		$('.med_champion_sprite').each(function(){
			$(this).removeClass('active');
			$(this).addClass('black-and-white');
		});
	}

	/*
		Transform the LC provided by API in words (Example: 1.4 to Platinum)
		Used in: Match Results Table
		Why: Facilitate the reading
	*/
	function getUserLeague(league){
		switch(league) {
			case 1.0:
				return 'Bronze';
				break;
	        case 1.1:
	          return 'Silver';
	          break;
	        case 1.25:
	          return 'Gold';
	          break;
	        case 1.4:
	          return 'Platinum';
	          break;
	        case 1.5:
	          return 'Diamond';
	          break;
	        case 1.55:
	          return  'Master';
	          break;
	        case 1.6:
	          return 'Challenger';
	          break;
	        default:
	          return 'Unranked';
	          break;
    	}
	}

	/* Hide a lot elements, do AJAX requests to API, prepare the match results table with rounded numbers and include Facebook Share */
	function doMatch(){
		/* Hide things from previous steps */
		$('#next').text('Back');
		$('#summoner-name').val('').hide();
		$('#select-region').hide();
		$('#champions_slider').hide();
		$('#introduction').hide();
		$('#match-header').hide();

		/* Start loading gif */
		$('#loading').show(300);

		/* Shows the match results wrapper */
		$('#match-wrapper').show(200);

		/* Search for the champion name by a champion id (used when the match comes from a direct link with hash) */
		for(var i=0; i < 2; i++){ /* 2 players */
			if(!match.championName[i]){
				var championsData = championsObj.data;
				for(champion in championsData){
					if(championsData[champion].id == match.champion[i]){
						match.championName[i] = championsData[champion].name;
					}
				}
			}
		}

		/* Shows to the user informations of the match */
		$('h3').text(
			match.user[0] + ' [' + match.region[0].toUpperCase() + '] ' + match.championName[0] + ' vs ' +
			match.user[1] + ' [' + match.region[1].toUpperCase() + '] ' + match.championName[1]
		);
		for(var i=0; i < 2; i++){
			$('.player-info.p'+i).text(
				match.user[i] + ' [' + match.region[i].toUpperCase() + '] ' + match.championName[i]
			);
		}

		/* Sets the website hash so that users can share the match */
		window.location.hash = match.region[0] + '-' + match.user[0].replace(' ', '') + '-' + match.champion[0] + '!vs!' + match.region[1] + '-' + match.user[1].replace(' ', '') + '-' + match.champion[1];

		/* Add the facebook Share+Like button so that users can share/like the match */
		$('#nav-p').html('<div class="fb-like" data-href="' + window.location.href + '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>');


		for(var i=0; i<2; i++){ /* 1 request for player */
			$.ajax({
			  type: 'GET',
			  url: apiUrl+'?region='+match.region[i]+'&username='+match.user[i]+'&champid='+match.champion[i],
			  contentType: 'text/plain',

			  xhrFields: {
			    withCredentials: false
			  },

			  success: function(data) {
			  	/* Parse the result JSON */
			  	var json = JSON.parse(data);

			  	/* Check which AJAX loaded first, and set the data to the right player */
			  	if(match.user[0] == json.user) {
			  		match.results[0] = json;
			  	}
			  	else {
			  		match.results[1] = json;
			  	}

			  	/* Check if both players results is already loaded */
			  	if(match.results[0] && match.results[1]){

						/* Hide the loading gif */
						$('#loading').hide(300);

						/* Shows the match results table */
						$('#results-table').show(400);

						/* Round stats of a summoner in 2 decimal places */
				  	for(var i = 0; i < 2; i++){
					  	$('.player-data.p'+i).each(function(){
					  		var stat = $(this).data('data');
				  			$(this).text(round(match.results[i].stats[stat], 2));
				  			if(i==0){
					  			p0.push(round(match.results[i].stats[stat], 2));
					  		}else{
					  			p1.push(round(match.results[i].stats[stat], 2));
					  		}
					  	});
				  	}

			  		/* Compare stats between players: won, lose or tie */
			  		for(var i=0;i<stats.length;i++){
			  			if(p0[i] > p1[i]){
			  				$('.player-data.p0.'+stats[i].toLowerCase()).addClass('won');
			  				$('.player-data.p1.'+stats[i].toLowerCase()).addClass('lose');
			  			} else if(p0[i] < p1[i]){
			  				$('.player-data.p0.'+stats[i].toLowerCase()).addClass('lose');
			  				$('.player-data.p1.'+stats[i].toLowerCase()).addClass('won');
			  			} else {
			  				$('.player-data.p0.'+stats[i].toLowerCase()).addClass('tie');
			  				$('.player-data.p1.'+stats[i].toLowerCase()).addClass('tie');
			  			}
			  		}

			  		/* Shows points of both players and compare then: won, lose or tie */
			  		for(var i = 0; i < 2; i++){
			  			$('.player-data-points.p'+i).text(match.results[i].points + ' points');
			  		}

			  		if(match.results[0].points > match.results[1].points){
		  				$('.player-data-points.p0').addClass('won');
		  				$('.player-data-points.p1').addClass('lose');
		  			} else if(match.results[0].points < match.results[1].points){
		  				$('.player-data-points.p0').addClass('lose');
		  				$('.player-data-points.p1').addClass('won');
		  			} else {
		  				$('.player-data-points.p0').addClass('tie');
		  				$('.player-data-points.p1').addClass('tie');
		  			}


		  			/* Transform LC in readable league name using getUserLeague */
			  		$('.player-data.p0.league').text(getUserLeague(p0[16]));
			  		$('.player-data.p1.league').text(getUserLeague(p1[16]));

			  		/* Transform the win rate in percentage */
			  		$('.player-data.p0.nmatcheswon').text(p0[14]*10+'%');
			  		$('.player-data.p1.nmatcheswon').text(p1[14]*10+'%');
			  	}
			  },

			  error: function(err) {
			  	console.log(err);
				$('#loading').hide(300);
			  }
			});
		}
	}

	/* Hide a lot of elements and backs to the first step (Player 1 information) */
	function resetMatch(){
		/* Reset the step and all match data */
		step = 0;
		match = {
			region: [],
			user: [],
			champion: [],
			championName: [],
			results: []
		};
		p0 = [];
		p1 = [];

		/* Reset match results */
		$('.player-data').each(function(){
			$(this).text('');
		});
		$('#results').text('');

		/* Reset the hash */
		window.location.hash = '';

		/* Hide the load and the result-table */
		$('#results-table').hide();
		$('#loading').hide();

		/* Backs to the step 1 */
		$('#match-header').show();
		$('#next').text('Next');
		$('#summoner-name').show();
		$('#select-region').show();
		$('#champions_slider').show();
		$('h3').text('Player ' + (step+1));
		$('#nav-p').text('Select the summoner and the champion');
	}

	/* Check if the user is coming from a direct link with hash */
	if(window.location.hash && window.location.hash != '#'){

		/* Splits the hash, remove spaces, # and !vs! */
		var splits = window.location.hash.replace(' ', '').replace('%20', '').replace('#', '').replace('!vs!', '-').split('-');

		/* Set match info by splitted the hash */
		match.region[0] = splits[0];
		match.user[0] = splits[1];
		match.champion[0] = splits[2];
		match.region[1] = splits[3];
		match.user[1] = splits[4];
		match.champion[1] = splits[5];

		/* Check if the URL is formatted correctly */
		if(match.user.length > 0 && match.champion.length > 0 && match.region.length > 0){
			doMatch();
			step = 3;
		} else {
			resetMatch(); /* Takes user back to step 1 */
		}
	}

	/* Champion search */
  	$("#search-champion").keyup(function(){
		var texto = $(this).val().toLowerCase();
		$(".champion_select").each(function(){
			if(texto == ''){ /* Check if the search input is empty and shows all champions*/
				$(this).show();
				$(this).parent().show();
			} else if($(this).attr('alt').toLowerCase().indexOf(texto) < 0){ /* Champion does not match the search, so hide it */
			 	$(this).hide();
				$(this).parent().hide();
			} else { /* Champion do match the search, so show it */
				$(this).show();
				$(this).parent().show();
			}
		});
	});

  	/* 'Select' visually the champion by adding a border and coloring */
	$("body").delegate(".med_champion_sprite", "click", function(){
		clearChampionSelect(); /* Reset the last selected champion and turn each one on black and white */
		$(this).addClass('active');

		/* Setting new informations of the player */
		match.champion[step] = $(this).attr('id');
		match.championName[step] = $(this).attr('alt');
	});

	/* When a user clicks on 'Start a match', take him/her to the step 1 */
	$('#start').click(function(){
		$('h3').text('Player 1');
		$('#nav-p').text('Select the summoner and the champion');
		$('#introduction').hide(200);
		$('#match-wrapper').delay(200).show(200);
	});

	/* When a user proceeds a step */
	$('#next').click(function(){
		if(step <= 1){ /* Check if its step 1 or 2 */
			match.region[step] = $('#select-region option:selected').text().toLowerCase();
			match.user[step] = $('#summoner-name').val();

			if(match.user[step].length > 0 && match.champion[step]){ /* Check if all the fields are filled - Region is checked by default */
				/* Reset data from previous steps */
				step++;
				clearChampionSelect();
				$('#summoner-name').val('').focus();
				$('#search-champion').val('');
				$(".champion_select").show();
				$(".champion_select").parent().show();
				$('h3').text('Player ' + (step+1));
			} else {
				alert('Please fill all the fields to proceed.');
			}
		}

		if(step == 2){ /* Check if its step 2 and do the match, proceeding to step 3 */
			doMatch();
			step++;
		} else if(step == 3) { /* The 'Back' button, backs to step 1 */
			resetMatch();
		}
	});

});
