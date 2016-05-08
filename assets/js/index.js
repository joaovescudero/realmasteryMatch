$(document).ready(function(){
	var match = {
		region: [],
		user: [],
		champion: [],
		championName: [],
		results: []
	};
	var step = 0;
	var p0=[];
	var p0points;
	var p1points;
	var p1=[];
	var list=["KDA", "Gold", "WardsPlaced", "KillingSpree", "DoubleKill", "TripleKill", "QuadraKill", "PentaKill", "CreepEarly", "CreepMid", "CreepLate", "NeutralCreeps", "TotalDamageDealt", "TotalDamageTaken", "nMatchesWon", "MasteryLevel", "league"];
	$('#results-table').hide();
	$('#loading').hide();
	$('#match-wrapper').hide();

	function round(value, decimals) {
    	return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
	}

	function clearChampionSelect() {
		$('.med_champion_sprite').each(function(){
			$(this).removeClass('active');
			$(this).addClass('black-and-white');
		});
	}

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

	function doMatch(){
		$('#next').text('Back');
		$('#summoner-name').val('').hide();
		$('#select-region').hide();
		$('#champions_slider').hide();
		$('#introduction').hide();
		$('#match-wrapper').show(200);
		$('h3').text(
			match.user[0] + ' [' + match.region[0].toUpperCase() + '] ' + match.championName[0] + ' vs ' +
			match.user[1] + ' [' + match.region[1].toUpperCase() + '] ' + match.championName[1]
		);
		$('#nav-p').html('<div class="fb-like" data-href="' + window.location.href + window.location.hash + '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>');

		match.championName[0] = match.championName[0] || match.champion[0];
		match.championName[1] = match.championName[1] || match.champion[1];
		
		$('#match-header').hide();

		window.location.hash = match.region[0] + '-' + match.user[0] + '-' + match.champion[0] + '!vs!' + match.region[1] + '-' + match.user[1] + '-' + match.champion[1];

		$('#loading').show(300);

		console.log('http://joaovescudero.me:8080/riot/api/?region='+match.region[0]+'&username='+match.user[0]+'&champid='+match.champion[0]);
		console.log('http://joaovescudero.me:8080/riot/api/?region='+match.region[1]+'&username='+match.user[1]+'&champid='+match.champion[1]);

		for(var i=0; i<2; i++){
			$.ajax({
			  type: 'GET',
			  url: 'http://joaovescudero.me:8080/riot/api/?region='+match.region[i]+'&username='+match.user[i]+'&champid='+match.champion[i],
			  contentType: 'text/plain',

			  xhrFields: {
			    withCredentials: false
			  },

			  success: function(data) {
			  	console.log(data);
			  	var json = JSON.parse(data);
			  	if(match.user[0] == json.user) {
			  		match.results[0] = json;
			  	}
			  	else {
			  		match.results[1] = json;
			  	}

			  	p0points = match.results[0].points;
			  	p1points = match.results[1].points;

			  	$('#results-table').show(400);

			  	if(match.results[0].user == match.user[0]){
				  	$('.player-data.p0').each(function(){
				  		var x = $(this).data('data');
			  			$(this).text(round(match.results[0].stats[x], 2));
				  		p0.push(round(match.results[0].stats[x], 2));
				  	});
			  	}
			  	if(match.results[1].user == match.user[1]){
				  	$('.player-data.p1').each(function(){
				  		var x = $(this).data('data');
			  			$(this).text(round(match.results[1].stats[x], 2));
				  		p1.push(round(match.results[1].stats[x], 2));
				  	});
			  	}

			  	if(match.results[0] && match.results[1]){
						$('#loading').hide(300);

			  		for(var i=0;i<list.length;i++){
			  			if(p0[i] > p1[i]){
			  				$('.player-data.p0.'+list[i].toLowerCase()).addClass('won');
			  				$('.player-data.p1.'+list[i].toLowerCase()).addClass('lose');
			  			}else if(p0[i] < p1[i]){
			  				$('.player-data.p0.'+list[i].toLowerCase()).addClass('lose');
			  				$('.player-data.p1.'+list[i].toLowerCase()).addClass('won');
			  			}else{
			  				$('.player-data.p0.'+list[i].toLowerCase()).addClass('tie');
			  				$('.player-data.p1.'+list[i].toLowerCase()).addClass('tie');
			  			}
			  		}

			  		$('.player-data-points.p0').text(p0points + ' points');
			  		$('.player-data-points.p1').text(p1points + ' points');

			  		$('.player-data.p0.league').text(getUserLeague(p0[16]));
			  		$('.player-data.p1.league').text(getUserLeague(p1[16]));

			  		$('.player-data.p0.nmatcheswon').text(p0[14]*10+'%');
			  		$('.player-data.p1.nmatcheswon').text(p1[14]*10+'%');

			  		if(p0points > p1points){
		  				$('.player-data-points.p0').addClass('won');
		  				$('.player-data-points.p1').addClass('lose');
		  			} else if(p0points < p1points){
		  				$('.player-data-points.p0').addClass('lose');
		  				$('.player-data-points.p1').addClass('won');
		  			} else{
		  				$('.player-data-points.p0').addClass('tie');
		  				$('.player-data-points.p1').addClass('tie');
		  			}
			  	}
			  },

			  error: function(err) {
			  	console.log(err);
			  }
			});
		}
	}

	function resetMatch(){
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
		p0points = [];
		p1points = [];

		$('.player-data').each(function(){
			$(this).text('');
		});

		window.location.hash = '';
		$('#results-table').hide();
		$('#loading').hide();
		$('#match-header').show();
		$('#results').text('');
		$('#next').text('Next');
		$('#summoner-name').show();
		$('#select-region').show();
		$('#champions_slider').show();
		$('h3').text('Player ' + (step+1));
		$('#nav-p').text('Select the summoner and the champion');
	}

  	$("#search-champion").keyup(function(){
		var texto = $(this).val().toLowerCase();
		$(".champion_select").each(function(){
			if(texto == ''){
				$(this).show();
				$(this).parent().show();
			} else if($(this).attr('alt').toLowerCase().indexOf(texto) < 0){
			  $(this).hide();
				$(this).parent().hide();
			} else {
				$(this).show();
				$(this).parent().show();
			}
		});
	});

	$('.med_champion_sprite').click(function(){
		clearChampionSelect();
		$(this).addClass('active');
		match.champion[step] = $(this).attr('id');
		match.championName[step] = $(this).attr('alt');
	});

	if(window.location.hash && window.location.hash != '#'){
		var splits = window.location.hash.replace(' ', '').replace('%20', '').replace('#', '').replace('!vs!', '-').split('-');
		match.region[0] = splits[0];
		match.user[0] = splits[1];
		match.champion[0] = splits[2];
		match.region[1] = splits[3];
		match.user[1] = splits[4];
		match.champion[1] = splits[5];

		if(match.user.length > 0 && match.champion.length > 0 && match.region.length > 0){
			doMatch();
			step = 3;
		} else {
			resetMatch();
		}
	}

	$('#next').click(function(){
		if(step <= 1){
			match.region[step] = $('#select-region option:selected').text().toLowerCase();
			match.user[step] = $('#summoner-name').val();

			if(match.user[step].length > 0 && match.champion[step]){
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

		if(step == 2){
			doMatch();
			step++;
		} else if(step == 3) {
			resetMatch();
		}
	});

	$('#start').click(function(){
		$('h3').text('Player 1');
		$('#nav-p').text('Select the summoner and the champion');
		$('#introduction').hide(200);
		$('#match-wrapper').delay(200).show(200);
	});
});