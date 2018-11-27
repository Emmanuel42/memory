var chk_cards_timeout = null;

$(document).ready(function(){
	$(".card").bind("click", toggleCard);
	$(".card").quickFlip();
	
	$(document).bind("found_match", matchingCards);
	$(document).bind("no_match", resetOnCards);
	//$(document).bind("card_closing", closeCard);
	$(document).bind("game_won", gameWon);
	$(document).bind("player_made_move", moveMade);
	$("#start_again").bind("click", reload);
	
	$("#level_chooser").bind("change", levelChoosen);
	
	// Add some sounds
	$(document).bind("flipping_cards.sound", playFlip);
	$(document).bind("game_won.sound", playCheer);
	$(document).bind("found_match", playMatch);
});

function levelChoosen(event){
	var $chooser = $(this);
	var level = $chooser.val();
	
	var url = document.location.href;
	if ( url.indexOf("?") != -1 ){
		url = url.substring(0, url.indexOf("?"));
	}
	
	document.location.replace(url + "?level="+level);
	
	$chooser = null;
};

function reload(event){
	event.preventDefault();
	document.location.reload();
	return false;
};

function gameWon(){
	$.getJSON(document.location.href, {won: 1}, notifiedServerWin);
	var $game_board = $("#game_board");
	var $player_won = $("#player_won");
	$game_board.hide();
	$player_won.show();
	$game_board = $player_won = null;
};

function notifiedServerWin(data){
	$("#start_again").show();
}

function resetOnCards(event){
	$cards = $(".on:visible");
	$.each($cards, function(index, card){
		$card = $(card);
		var css_class = $card.parent(".card").metadata()["toggle"];
		$card.trigger("card_closing");
		$card.removeClass(css_class);
		$card.parent(".card").quickFlipper();
		$card = null;
	});
	$cards = null;
};

function matchingCards(event, params){
	$cards = $("."+params.css_class+".on:visible");
	$.each($cards, function(index, card){
		var $card = $(card);
		$card.trigger("card_removed");
		$card.parent(".card").unbind("*").before("<div class='card_ph'></div>").remove();
		$card = null;
	});
	
	$cards_left = $("#game_board>.card");
	if ( $cards_left.length == 0 ){
		$(document).trigger("game_won", {});
		/* 
		 * quickFlip has a problem when working in IE: when the last 
		 * element bound was removed the problem is caused by the bound 
		 * resize event on the window and is causing 
		 * the end game get stuck when the game is over...
		 */
		$(window).unbind("resize");
	}
	$cards_left = $cards = null;
};

function toggleCard(event){
	if ( chk_cards_timeout != null ){
		clearTimeout(chk_cards_timeout);
		chk_cards_timeout = null;
		checkCards();
	}
	var $card = $(this);
	if($card.children(".off").is(":visible")){
		$(document).trigger("flipping_cards");
		var num_already_opened = $card.parent("#game_board").find(".card>.on:visible").length;
		var css_class = $card.metadata()["toggle"];
		$card.children(".on").addClass(css_class);
		$card.quickFlipper();
		
		if ( num_already_opened == 1 ){
			chk_cards_timeout = setTimeout(checkCards, 1000);
		}
	}
	$card = null;
};

function checkCards(){
	$on_cards = $("#game_board .card>.on:visible");
	if ( $on_cards.length == 2 ){
		$(document).trigger("player_made_move");
		// Get the first object css class
		var css_class = $on_cards.parent(".card").metadata()["toggle"];
		$matched_cards = $on_cards.filter("."+css_class);
		var event_name = "no_match";
		if ( $matched_cards.length == 2 ){
			event_name = "found_match";
		} 
		$(document).trigger(event_name, {css_class: css_class});
		$matched_cards = null;
	}
	clearTimeout(chk_cards_timeout);
	chk_cards_timeout = null;
	$on_cards = null;
};

function moveMade(){
	var $moves_tracker = $("#num_of_moves");
	var num_of_moves = $moves_tracker.data("moves") || 0;
	++num_of_moves;
	$moves_tracker.data("moves", num_of_moves).text(num_of_moves);
	$moves_tracker = null;
};

function getFlashMovieObject(movieName){
	if (window.document[movieName]){
		return window.document[movieName];
	}
	if (navigator.appName.indexOf("Microsoft Internet")==-1){
		if (document.embeds && document.embeds[movieName])
			return document.embeds[movieName];
	} else {
		return document.getElementById(movieName);
	}
};

function playCheer(){
	var sfxMovie=getFlashMovieObject("sfx_movie");
	try{
		sfxMovie.cheer();
	} catch(e) {};
};

function playFlip(){
	var sfxMovie=getFlashMovieObject("sfx_movie");
	try{
		sfxMovie.flip();
	} catch(e) {};
};

function playMatch(){
	var sfxMovie=getFlashMovieObject("sfx_movie");
	try{
		sfxMovie.match();
	} catch(e) {};
};
