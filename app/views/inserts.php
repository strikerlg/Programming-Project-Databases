<?php		Stats::addContinent("Europe");
		Stats::addCountry("Belgium", "Europe", "be");
		Stats::addCompetition("World Cup");
		Stats::addCoach("Marc Wilmots");
		Stats::addTeam("Belgium", "Belgium", "Marc Wilmots");
		
		Stats::addCountry("Russia", "Europe", "ru");
		Stats::addCoach("Fabio Capello");
		Stats::addTeam("Russia", "Russia", "Fabio Capello");
		
		Stats::addTeamPerCompetition("Belgium", "World Cup");
		Stats::addTeamPerCompetition("Russia", "World Cup");
		Stats::addMatch("Belgium", "Russia", "World Cup");
		
		Stats::addPlayerUnique("Vincent Kompany", 0);
		Stats::addPlayerPerTeam("Vincent Kompany", "Belgium");
		
		Stats::addPlayerUnique("Igor Denisov", 0);
		Stats::addPlayerPerTeam("Igor Denisov", "Russia");
		
		Stats::addPlayerUnique("Dries Mertens", 0);
		Stats::addPlayerPerTeam("Dries Mertens", "Belgium");
		
		Stats::addPlayerUnique("Nacer Chadli", 0);
		Stats::addPlayerPerTeam("Nacer Chadli", "Belgium");
		
		Stats::addPlayerUnique("Thibaut Courtois", 0);
		Stats::addPlayerPerTeam("Thibaut Courtois", "Belgium"); ?>
