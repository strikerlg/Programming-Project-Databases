<?php

class Team {
	public static function getTeambyID($askedID){
		$result = DB::select('SELECT * FROM team WHERE id = ?', array($askedID));
		return $result;
	}
	
	public static function getAll(){
		$result = DB::select('SELECT * FROM team');
		return $result;
	}
	
	public static function getTeambyPlayerID($playerID) {
		$teamID = DB::select('SELECT team_id FROM playerPerTeam WHERE player_id = ?', array($playerID));
		$result = Team::getTeambyID($teamID[0]->team_id);
		return $result;
	}
	
	public static function getPlayers($teamID) {
		$playerIDs = DB::select('SELECT player_id FROM playerPerTeam WHERE team_id = ?', array($teamID));
		$players = array();
		foreach ($playerIDs as $playerID) {
			$player = DB::select('SELECT * FROM player WHERE id = ?', array($playerID->player_id));
			array_push($players, $player);
		}
		
		return $players;
	}

	public static function getFIFAPoints() {
			$results = DB::select('SELECT name FROM country');
			$points = array();
			foreach ($results as $country) {
				$fifaPoints = DB::select('SELECT fifapoints FROM team WHERE name = ?', array($country->name));
				if (!empty($fifaPoints)) {
					$thesePoints = array("name" => $country->name, "points" => $fifaPoints[0]->fifapoints);
					array_push($points, $thesePoints);
				}
			}
			return $points;
	}
	
	public static function getTeamText($teamName){
		$jsonurl = "http://en.wikipedia.org/w/api.php?action=query&prop=extracts&format=json&exsentences=5&exlimit=10&exintro=&exsectionformat=plain&titles=" . urlencode($teamName);
		$json = file_get_contents($jsonurl);
		$decodedJSON = json_decode($json, true, JSON_UNESCAPED_UNICODE);
		
		foreach ($decodedJSON['query']['pages'] as $key => $value) {
			$pagenr = $key;
		}		
		
		$text = $decodedJSON['query']['pages'][$pagenr]['extract'];
		return $text;
	}
	
	public static function getTeamImageURL($teamName) {
		$jsonurl = "http://en.wikipedia.org/w/api.php?action=query&titles=" . urlencode($teamName) . "&prop=pageimages&format=json&pithumbsize=300";
		$json = file_get_contents($jsonurl);
		$decodedJSON = json_decode($json, true, JSON_UNESCAPED_UNICODE);
		
		foreach ($decodedJSON['query']['pages'] as $key => $value) {
			$pagenr = $key;
		}
		
		$url = $decodedJSON['query']['pages'][$pagenr]['thumbnail']['source'];
		
		return $url;
	}
}
