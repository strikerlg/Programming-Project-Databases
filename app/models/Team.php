<?php

/**
 * @class Team
 * @brief The core data model of a team.
 */
class Team {

    /**
     * @var TABLE_TEAM
     * @brief the team table.
     */
    const TABLE_TEAM            = "team";

    /**
     * @var TABLE_PLAYER_PER_TEAM
     * @brief Table where each player is linked to a team.
     */
    const TABLE_PLAYER_PER_TEAM = "playerPerTeam";

    /**
     * @brief Get the IDs of the team by inputting the name only.
     * @param name The name of the team.
     * @return Results after the query.
     */
    public static function getIDsByName( $name ) {
        $query = "SELECT id FROM `".self::TABLE_TEAM."` WHERE name = (?)";
        $values = array( $name );

        return DB::select( $query, $values );
    }

    /**
     * @brief add a team into the data model.
     * @param name The name of the team.
     * @param coach_id The coach ID of the team.
     * @param country_id The ID of the country this team is active.
     * @param points The FIFA points of the team.
     * @return The IDs of the team just added.
     */
    public static function add( $name, $coach_id, $country_id, $points ) {
        $query = ( empty( $coach_id ) ) ? "INSERT INTO `".self::TABLE_TEAM."` (name, country_id, fifapoints) VALUES ( ?, ?, ?)" : "INSERT INTO `".self::TABLE_TEAM."` (name, country_id, coach_id, fifapoints) VALUES ( ?, ?, ?, ?)";
        $values = ( empty( $coach_id ) ) ? array( $name, $country_id, $points ) : array( $name, $country_id, $coach_id, $points );

        DB::insert( $query, $values );

        return self::getIDsByName( $name );
    }

    public static function update($team_id, $points, $coach_id) {
        $query = "UPDATE `".self::TABLE_TEAM."` SET fifapoints = ?, coach_id = ? WHERE id = ?";
        $values = array($points, $coach_id, $team_id);

        DB::update($query, $values);
        return;
    }

    /**
     * @brief link a player to a team.
     * @param player_id The player ID.
     * @param team_id The team ID.
     * @return True if new link created, False otherwise.
     */
    public static function linkPlayer( $player_id, $team_id, $position ) {
        // first check whether the link was already created
        $query = "SELECT * FROM `".self::TABLE_PLAYER_PER_TEAM."` WHERE player_id = ? AND team_id = ?";
        $values = array( $player_id, $team_id );
        $sql = DB::select( $query, $values );
        if ( !empty( $sql ) ) return False;

        $query = 'INSERT INTO `'.self::TABLE_PLAYER_PER_TEAM.'` (player_id, team_id, position) VALUES (?, ?, ?)';
        $values = array( $player_id, $team_id, $position );
        DB::insert( $query, $values );
        return True;
    }

    /**
     * @brief Get the team by id.
     * @param id The id of the team.
     * @return Results after the query.
     */
    public static function getTeambyID( $id ){
        $query = "SELECT * FROM `".self::TABLE_TEAM."` WHERE id = ?";
        $values = array( $id );

        return DB::select( $query, $values );
    }

    /**
     * @brief Get all the teams.
     * @return Results after the query.
     */
    public static function getAll(){
        $query = "SELECT team.id, team.name, team.fifapoints, country.abbreviation, continent.name AS continent FROM team, country, continent WHERE team.country_id = country.id AND country.continent_id = continent.id";

        return DB::select( $query );
    }

    /**
     * @brief Get the team of the player (by ID).
     * @param playerID The ID of the player.
     * @return Result after the query.
     */
    public static function getTeambyPlayerID( $playerID ) {
        // query for teamID
        $query = "SELECT team_id FROM `".self::TABLE_PLAYER_PER_TEAM."` WHERE player_id = ?";
        $values = array( $playerID );

        $teamID = DB::select( $query, $values );

        return ( empty( $teamID ) ) ? NULL : Team::getTeambyID( $teamID[0]->team_id );
    }

    /**
     * @brief Get the team ID of the player (by ID).
     * @param playerID The ID of the player.
     * @return Result after the query.
     */
    public static function getTeamIDbyPlayerID( $playerID ) {
        // query for teamID
        $query = "SELECT team_id FROM `".self::TABLE_PLAYER_PER_TEAM."` WHERE player_id = ?";
        $values = array( $playerID );

        $teamID = DB::select( $query, $values );

        return ( empty( $teamID ) ) ? NULL : $teamID[0]->team_id;
    }
    /**
     * @brief Get all the players of a given team.
     * @param teamID The ID of the team.
     * @return Array of the players.
     */
    public static function getPlayers( $teamID ) {
        $result = DB::select("SELECT player.id, player.name, (SELECT position FROM playerPerTeam WHERE player_id = player.id AND team_id = ?) as position FROM player WHERE  player.id IN (SELECT player_id FROM playerPerTeam WHERE team_id = ?)", array($teamID,$teamID));

        return $result;
    }

    /**
     * @brief Get all the FIFA points of each international teams.
		 * @param geoCharts Team names GeoCharts-compatible if true (default false)
     * @return array where each country is mapped to a point.
     */
    public static function getFIFAPoints($geoCharts=false) {
        $query = "SELECT id, name, fifapoints FROM `".self::TABLE_TEAM."`";

        $records = DB::select( $query );

        $points = array();
        foreach ( DB::select( $query ) as $row ) {
            $thesePoints = array(
				"id"        => $row->id,
                "name"      => $row->name,
                "points"    => $row->fifapoints
            );

            array_push( $points, $thesePoints );
        } // end foreach

				//Change/add some countries so that array is complete and correct if it's meant for a GeoCharts map
				if ($geoCharts) {
					foreach ($points as &$row) {
						//Manually change names of unrecognized national teams
						if ($row["name"] == "China PR")
							$row["name"] = "China";
						if ($row["name"] == "Congo DR")
							$row["name"] = "Democratic Republic of the Congo";
						if ($row["name"] == "Korea DPR")
							$row["name"] = "North Korea";
						if ($row["name"] == "Korea Republic")
							$row["name"] = "South Korea";
						if ($row["name"] == "Macedonia FYR")
							$row["name"] = "Former Yuguslavian Republic of Macedonia";
						if ($row["name"] == "Chinese Taipei")
							$row["name"] = "Taiwan";
					}
					//Add countries without national team (in database) with 0 FIFA Points
					//Note that id 0 is invalid, it indicates the country has no team in the db
          array_push( $points, array("id" => 0, "name" => "Svalbard and Jan Mayen", "points" => 0) );
          array_push( $points, array("id" => 0, "name" => "French Guiana", "points" => 0) );
          array_push( $points, array("id" => 0, "name" => "Greenland", "points" => 0) );
          array_push( $points, array("id" => 0, "name" => "Western Sahara", "points" => 0) );
          array_push( $points, array("id" => 0, "name" => "Kosovo", "points" => 0) );
				}

        return $points;
    }

    /**
     * @brief Get the first best teams.
     * @param limit The limit.
     * @return Results after the query.
     */
    public static function getTopTeams($limit = ''){
        $limit = ( '' != $limit && is_numeric( $limit ) ) ? "LIMIT ".$limit : $limit;

        $query = "SELECT team.id, team.name, team.fifapoints, country.abbreviation FROM team, country WHERE country.id = team.country_id ORDER BY team.fifapoints desc ".$limit;
        return DB::select( $query );
    }

    /**
     * @brief Get the matches of a given team.
     * @param teamID The ID of the team.
     * @return Results after the query.
     */
    public static function getMatches( $teamID ){
        $query = "SELECT `match`.date,
            `match`.id as match_id,
            (SELECT name FROM team WHERE team.id = `match`.hometeam_id) as hometeam,
            (SELECT name FROM team WHERE team.id = `match`.awayteam_id) as awayteam,
			competition_id,
			(SELECT name FROM competition WHERE competition.id = `match`.competition_id) as competition
            FROM `match` WHERE hometeam_id = ? OR awayteam_id = ?";
        $values = array( $teamID, $teamID );

        return DB::select( $query, $values );
    }

    /**
     * @brief Get the team's summary.
     * @param name The name of the team.
     * @return The team's biography (summary).
     */
    public static function getTeamText( $name ){
      	$editedName = $name . " national football team";
        if ($name == "United States") {
          $editedName = "United States men's national soccer team";
        }

        $jsonurl = "http://en.wikipedia.org/w/api.php?action=query&prop=extracts&format=json&exsentences=5&exlimit=10&exintro=&exsectionformat=plain&titles=" . urlencode( $editedName );

        $json = file_get_contents($jsonurl);
        $decodedJSON = json_decode($json, true, JSON_UNESCAPED_UNICODE);

        foreach ($decodedJSON['query']['pages'] as $key => $value) {
            $pagenr = $key;
        } // end foreach

        try {
            return $decodedJSON['query']['pages'][$pagenr]['extract'];
        }
        catch (Exception $e) {
            return "No summary available.";
        } // end try-catch
    }

    /**
     * @brief Get the team image URL.
     * @param name The name of the team.
     * @return The image URL of the team.
     */
    public static function getTeamImageURL( $name ) {
        $jsonurl = "http://en.wikipedia.org/w/api.php?action=query&titles=" . urlencode( $name ) . "&prop=pageimages&format=json&pithumbsize=300";

        $json = file_get_contents($jsonurl);
        $decodedJSON = json_decode($json, true, JSON_UNESCAPED_UNICODE);

        foreach ($decodedJSON['query']['pages'] as $key => $value) {
            $pagenr = $key;
        } // end foreach

        try {
            return $decodedJSON['query']['pages'][$pagenr]['thumbnail']['source'];
        }
        catch (Exception $e) {
            return "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcQDBQGDMwwKrrjyl5frVZhTV1qDP6u3YtPhFW_XM6zjdStHkm0";
        } // end try-catch
    }

	public static function getWinsLossesTies( $teamID ) {
		$matches = Team::getMatches( $teamID );
		$wins = 0;
		$losses = 0;
		$ties = 0;
		foreach ($matches as $match) {
			if (!Match::isPlayed($match->match_id))
				continue;
			if (Team::getIDsByName($match->hometeam)[0]->id == $teamID) {
				$thisTeam = 0;
				$otherTeam = 1;
			}
			else {
				$thisTeam = 1;
				$otherTeam = 0;
			}
			$score = Match::getScore2($match->match_id);
			if ($score[$thisTeam] > $score[$otherTeam])
				$wins += 1;
			if ($score[$thisTeam] == $score[$otherTeam])
				$ties += 1;
			if ($score[$thisTeam] < $score[$otherTeam])
				$losses += 1;
		}
		return array("wins" => $wins, "losses" => $losses, "ties" => $ties);
	}



	public static function getYearlyGoalsCards( $teamID ) {
		$matches = Team::getMatches( $teamID );
		$stats = array();
		foreach ($matches as $match) {
			if (!Match::isPlayed($match->match_id) or $match->date == "0000-00-00 00:00:00")
				continue;
			$matchYear = new DateTime($match->date);
			$matchYear = $matchYear->format("Y");
			$cards = Match::getCardCounts($match->match_id);
			if (Team::getIDsByName($match->hometeam)[0]->id == $teamID) {
				$yellows = $cards[0];
				$reds = $cards[1];
				$score = Match::getScore2($match->match_id)[0];
			}
			else {
				$yellows = $cards[2];
				$reds = $cards[3];
				$score = Match::getScore2($match->match_id)[1];
			}
			if (array_key_exists($matchYear, $stats)) {
				$stats[$matchYear]["totalScore"] = $stats[$matchYear]["totalScore"] + $score;
				$stats[$matchYear]["totalYellows"] = $stats[$matchYear]["totalYellows"] + $yellows;
				$stats[$matchYear]["totalReds"] = $stats[$matchYear]["totalReds"] + $reds;
				$stats[$matchYear]["matchCount"] = $stats[$matchYear]["matchCount"] + 1;
			}
			else
				$stats[$matchYear] = array("totalScore" => $score, "totalYellows" => $yellows, "totalReds" => $reds, "matchCount" => 1);
		}
		return ($stats);
	}

    public static function getFifaPointsByID( $teamID ) {
        $results = DB::select('SELECT team.fifapoints FROM `team` WHERE id = ?', array($teamID));
        return $results[0];
    }



}
