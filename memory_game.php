<?php	
	session_start();
	
	// first check if we have a utility function
	if ( isset($_REQUEST["won"]) ){
		// si on a gagné, on réinitialise la grille
		unset($_SESSION['board']);
		$_SESSION['games_won'] = ++$_SESSION['games_won'];
		$response = array("status" => "ok");
		exit(json_encode($response));
	}
	
	// All the card files we have
	$CARDS = array("images/image0001.png",
					"images/image0002.png","images/image0003.png",
					"images/image0004.png","images/image0005.png",
					"images/image0006.png","images/image0007.png",
					"images/image0008.png","images/image0009.png",
					"images/image0010.png","images/image0011.png",
					"images/image0012.png","images/image0013.png",
					"images/image0014.png","images/image0015.png",
					"images/image0016.png","images/image0017.png",
					"images/image0018.png","images/image0019.png",
					"images/image0020.png");
	
	class Board
	{
		private $css = array();
		private $cards = array();
		private $cards_names = array();
		private $cols = 0;
		private $rows = 0;
		private $modes = array(6, 8, 10, 12, 15, 18);
		
		function __construct($level, $card_files) {
			$num_of_cards = $this->modes[$level - 1];
			
			// Shuffle the cards available so we won't pick the 
			// same ones every time
			shuffle($card_files);
			// Get the card objects
			$cards = array();
			for ( $i = 0; $i < $num_of_cards; ++$i ){
				$cards[$i] = new Card($card_files[$i]);
				$this->css[] = $cards[$i]->get_css_block();
			}
			// Double the array so we will have pairs
			$this->cards = array_merge($cards, $cards);
			
			// Shuffle the cards to create the order on the board
			shuffle($this->cards);
			
			// Get the number of cols
			$num = count($this->cards);
			$sr = sqrt($num);
			$this->rows = floor($sr);
			while ( $num % $this->rows ){
				--$this->rows;
			}
			$this->cols = $num / $this->rows;
		}
		
		function max_level(){
			return count($this->modes);
		}
		
		function get_css(){
			return implode("\n",$this->css);
		}
		
		function debug_print(){
			$p_rslt = array("cards"=>$this->cards, "rows"=>$this->rows, "cols"=>$this->cols);
			print "<br/ >".json_encode($p_rslt);
		}
		
		function get_rows(){
			return $this->rows;
		}
		
		function get_cols(){
			return $this->cols;
		}
		
		function get_cards(){
			return $this->cards;
		}
		
		
		function get_size(){
			return count($this->cards);
		}
		
		function get_card($index){
			return $this->cards[$index];
		}
		
		function get_html(){
			// For each card
			for ( $i = 0 ; $i < $this->get_size() ; ++$i ){
				// Check if it's time for a new row
				if ( ($i % $this->get_cols()) == 0 ){
					print "\r<div class=\"clear\"></div>";
				}
				print $this->get_card($i)->get_html_block();
			}
		}
	}

	class Card{	
		private $css_class = "";
		private $url = "";
		
		function __construct($url) {
			$this->url = $url;
			$this->css_class = $this->extract_name($url);
		}
		
		function get_name(){
			return $this->css_class;
		}
		
		function get_css_block(){
			return "\n.".$this->get_name()."{background:url(".$this->url.") center center no-repeat;}";
		}
		
		function get_html_simple_block(){
			return "\r<div class=\"card {toggle:'".$this->get_name()."'}\"></div>";
		}
		
		function get_html_block(){
			return "\r<div class=\"card {toggle:'".$this->get_name()."'}\">
						\r<div class=\"off\"></div>
						\r<div class=\"on\"></div>
					</div>";
		}
		private function extract_name($str){
			$tmp = pathinfo($str);
			return $tmp['filename'];
		}
	}

	$level = 1;
	if (!isset($_SESSION['games_won'])) {
		$_SESSION['games_won'] = 0;
	}
	
	if (isset($_REQUEST['level']) ) {
		$level = $_REQUEST['level'];
		
		$board = new Board($level, $CARDS);
		$_SESSION['board'] = $board;
	} else {
		if (!isset($_SESSION['board'])) {
			$board = new Board($level, $CARDS);
			$_SESSION['board'] = $board;
		} else {
			$board = $_SESSION['board'];
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<title>Memory Cards :: Basic Demo</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<link rel="stylesheet" href="memory_game.css" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	<script type="text/javascript" src="jquery.metadata.js"></script>
	<script type="text/javascript" src="jquery.quickflip.js"></script>
	<script type="text/javascript" src="memory_game.js"></script>
	<script type="text/javascript" src="swfobject.js"></script>
<script type="text/javascript">
	var flashvars = false;
	var attributes = {};
	var params = {
	  allowscriptaccess : "always",
	  wmode : "transparent",
	  menu: "false"
	};
	swfobject.embedSWF("sfx.swf", "sfx_movie", "1", "1", "9.0.0", "expressInstall.swf", flashvars, params, attributes);
</script>
</head>
<style>
	<?php 
		print $board->get_css();
	?>
</style>
<body>
<h3>Simple Memory Game</h3>

<div id="control" style="width:<?php print $board->get_cols()*75; ?>px;">
	<label>Level:</label>
	<select id="level_chooser">
		<?php 
			print "<!-- ".$board->max_level()." -->";
			for ( $i = 0; $i < $board->max_level(); ++$i ){
					$selected = ( ($i+1) == $level ) ? " selected=selected" : "";
					print "\r<option value=\"".($i+1)."\"".$selected.">".($i+1)."</option>";
			}
		?>
		
	</select>
	<label>Parties terminées: </label>
	<span><?php print $_SESSION["games_won"]; ?></span>
	<label>Moves:</label>
	<span id="num_of_moves">0</span>
</div>
<div id="game_board" style="width:<?php print $board->get_cols()*75; ?>px;">
<?php
	print $board->get_html();
?>
</div>
<div id="player_won"></div>
<div id="start_again"><a id="again" href="#">Cliquez ici pour rejouer</a></div>

</body>
</html>
