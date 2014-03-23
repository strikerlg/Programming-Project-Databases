<?php

use Symfony\Component\DomCrawler\Crawler;

// don't complain when the HTML document is not valid
libxml_use_internal_errors( true );

/**
 * @brief Request the page.
 * @details Sometimes, the site is too busy, but you can then send another 
 * request again.
 *
 * @param limit The limit.
 *
 * @throw ErrorException when limit has exceeded.
 * @return DOMDocument
 */
function request( &$domdocument, $url, $limit=5 ) {
    try {
        $domdocument->loadHTMLFile( $url );
    } catch ( ErrorException $ee ) {

        if ( 0 == $limit ) {
            // beyond limit
            throw $ee;
        } else {
            // try again
            return request( $domdocument, $url, $limit-1 );
        } // end if-else

    } // end try-catch
}

/**
 * @class CrawlerController
 * @brief Crawl data from site.
 */
class CrawlerController extends BaseController {

    /**
     * @brief Generator for all countries participating in the men's national 
     * association football teams, it's abbreviation and it's continent.
     * abbreviation and it's continent.
     * @details A complete list can be found at
     * https://en.wikipedia.org/wiki/List_of_FIFA_country_codes
     *
     * @return An associative array with the following values mapped:
     *          "country"       => $country,
     *          "continent"     => $continent,
     *          "abbreviation"  => $abbreviation
     */
    public static function countries() {
        // load document
        $doc = new DOMDocument();

        try {
            request( $doc, "https://en.wikipedia.org/wiki/List_of_FIFA_country_codes" );
        } catch ( ErrorException $ee ) {
            // HTTP request failed
            return;
        } // end try-catch

        $crawler = new Crawler();
        $crawler->addDocument( $doc );

        foreach ( $crawler->filterXPath( '//table[@class="wikitable"]/tr/td/a[contains(@title, "team")]/../..') as $row ) {
            $country = $row->getElementsByTagName( 'a' );
            if ( empty( $country ) ) { continue; }
            # TODO Cannot fix that goddamn continent
            #$country_href = $country->item(0)->getAttribute( 'href' );
            $country = trim( $country->item(0)->textContent );

            $abbreviation = $row->getElementsByTagName( 'td' );
            if ( 2 > $abbreviation->length ) { continue; }
            $abbreviation = trim( $abbreviation->item(1)->textContent );

            #$country_page = new DOMDocument();

            #try {
            #    $country_page->loadHTMLFile( "https://en.wikipedia.org/".$country_href );
            #} catch ( ErrorException $ee ) {
            #    // HTTP request failed
            #    continue;
            #} // end try-catch

            #$country_crawler = new Crawler();
            #$country_crawler->addDocument( $country_page );

            $continent = "";
            #if ( empty( $continent ) ) { continue; }
            #$continent = trim( $continent->textContent );

            yield array(
                "country"       => $country,
                "continent"     => $continent,
                "abbreviation"  => $abbreviation
            );

            #$country_crawler->clear();
        } // end foreach

        // clear crawler (to avoid memory exhausting)
        $crawler->clear();
        return;
    }

    /**
     * @brief Generator for all teams.
     * @details For the participant lists, see:
     * http://int.soccerway.com/teams/rankings/fifa/
     *
     * @return An associative array with the following values mapped:
     *          "href"      => $href,
     *          "name"      => $team,
     *          "rank"      => $rank,
     *          "points"    => $points,
     *          "coach"     => $first_name.' '.$last_name,
     *          "logo"      => $logo,
     *          "founded"   => $founded,
     *          "address"   => $address,
     *          "country"   => $country,
     *          "phone"     => $phone,
     *          "fax"       => $fax,
     *          "email"     => $email
     */
    public static function teams() {
        // load document
        $doc = new DOMDocument();

        try {
            request( $doc, "http://int.soccerway.com/teams/rankings/fifa/" );
        } catch ( ErrorException $ee ) {
            // HTTP request failed
            return;
        } // end try-catch

        $crawler = new Crawler();
        $crawler->addDocument( $doc );

        foreach ( $crawler->filterXPath( '//table[contains(@class, fifa_rankings)]/tbody/tr/td/..' ) as $row ) {
            $data = $row->getElementsByTagName( 'td' );

            $rank = $data->item(0);
            if ( empty( $rank ) ) { continue; }
            $rank = trim( $rank->textContent );

            $team = $data->item(1);
            if ( empty( $team ) ) { continue; }
            $team = trim( $team->textContent );

            $points = $data->item(2);
            if ( empty( $points ) ) { continue; }
            $points = trim( $points->textContent );

            $href = $data->item(1)->getElementsByTagName( 'a' );
            $href = $href->item(0)->getAttribute( 'href' );
            if ( empty( $href ) ) { continue; }

            // now navigate to the team page
            $team_page = new DOMDocument();

            try {
                request( $team_page, "http://int.soccerway.com/".$href );
            } catch ( ErrorException $ee ) {
                // HTTP request failed
                continue;
            } // end try-catch

            $team_crawler = new Crawler();
            $team_crawler->addDocument( $team_page );

            $logo = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div[@class="logo"]/img' );
            $logo = empty( $logo->getNode(0) ) ? "" : $logo->getNode(0)->getAttribute( 'src' );

            $founded = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div/dl/dd[1]' );
            $founded = empty( $founded->getNode(0) ) ? "" : trim( $founded->getNode(0)->textContent );

            $address = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div/dl/dd[2]' );
            $address = empty( $address->getNode(0) ) ? "" : trim( $address->getNode(0)->textContent );

            $country = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div/dl/dd[3]' );
            if ( empty( $country->getNode(0) ) ) { continue; }
            $country = trim( $country->getNode(0)->textContent );

            $phone = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div/dl/dd[4]' );
            $phone = empty( $phone->getNode(0) ) ? "" : trim( $phone->getNode(0)->textContent );

            $fax = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div/dl/dd[5]' );
            $fax = empty( $fax->getNode(0) ) ? "" : trim( $fax->getNode(0)->textContent );

            $email = $team_crawler->filterXPath( '//div[contains(@class, block_team_info)]/div/div/dl/dd[6]' );
            $email = empty( $email->getNode(0) ) ? "" : trim( $email->getNode(0)->textContent );

            $coach_href = $team_crawler->filterXPath( '//table[contains(@class, squad)]/tbody[5]/tr/td[2]/div/a' );
            if ( empty( $coach_href->getNode(0) ) ) { continue; }
            $coach_href = $coach_href->getNode(0)->getAttribute( 'href' );

            // to the coach page to get his full name
            $coach_page = new DOMDocument();

            try {
                request( $coach_page, "http://int.soccerway.com/".$coach_href );
            } catch ( ErrorException $ee ) {
                // HTTP request failed
                $team_crawler->clear();
                continue;
            } // end try-catch

            $coach_crawler = new Crawler();
            $coach_crawler->addDocument( $coach_page );

            $first_name = $coach_crawler->filterXPath( '//div[contains(@class, block_player_passport)]/div/div/div/div/dl/dd[1]' );
            if ( empty( $first_name->getNode(0) ) ) { continue; }
            $first_name = trim( $first_name->getNode(0)->textContent );

            $last_name = $coach_crawler->filterXPath( '//div[contains(@class, block_player_passport)]/div/div/div/div/dl/dd[2]' );
            if ( empty( $last_name->getNode(0) ) ) { continue; }
            $last_name = trim( $last_name->getNode(0)->textContent );

            yield array(
                "href"      => $href,
                "name"      => $team,
                "rank"      => $rank,
                "points"    => $points,
                "coach"     => $first_name.' '.$last_name,
                "logo"      => $logo,
                "founded"   => $founded,
                "address"   => $address,
                "country"   => $country,
                "phone"     => $phone,
                "fax"       => $fax,
                "email"     => $email
            );

            $coach_crawler->clear();
            $team_crawler->clear();
        } // end foreach

        // clear crawler to avoid memory exhausting
        $crawler->clear();
        return;
    }

    /**
     * @brief Generator for all matches.
     * @details Based upon the matches on
     * http://int.soccerway.com/international/world/world-cup/2014-brazil/s6395/
     *
     * @returns Array with info about the match and thus the following values 
     * mapped:
     *          'competition'   => $competition,
     *          'date'          => $date,
     *          'time'          => $time,
     *          'score'         => $score,
     *          'home team'     => $hometeam,
     *          'away team'     => $awayteam,
     */
    public static function matches( $url='http://int.soccerway.com/international/world/world-cup/c72/' ) {
        $doc = new DOMDocument();

        try {
            request( $doc, $url );
        } catch ( ErrorException $ee ) {
            // HTTP request failed
            return;
        } // end try-catch

        $crawler = new Crawler();
        $crawler->addDocument( $doc );

        $competition = $crawler->filterXPath( '//div[contains(@class, block_competition_left_tree)]/ul/li/ul/li/a');
        if ( empty( $competition->getNode(0) ) ) { continue; }
        $competition = 'World Cup '.$competition->getNode(0)->textContent;

        foreach ( $crawler->filterXPath( '//div[contains(@class, block_competition_matches)]/div/table/tbody/tr' ) as $row ) {
            $data = $row->getElementsByTagName( 'td' );

            $date = $data->item(1);
            if ( empty( $date ) ) { continue; }
            $date = DateTime::createFromFormat('j/m/y', $date->textContent)->format('Y-m-d');

            $hometeam = $data->item(2);
            if ( empty( $hometeam ) ) { continue; }
            $hometeam = trim( $hometeam->textContent );

            $awayteam = $data->item(4);
            if ( empty( $awayteam ) ) { continue; }
            $awayteam = trim( $awayteam->textContent );

            $status = $data->item(3);
            if ( empty( $status ) ) { continue; }
            $time =  (1 == substr_count( $status->textContent, ':' ) ) ? trim( $status->textContent ) : "";
            $score = (1 == substr_count( $status->textContent, '-' ) ) ? trim( $status->textContent ) : "0 - 0";

            yield array(
                'competition'   => $competition,
                'date'          => $date,
                'time'          => $time,
                'score'         => $score,
                'home team'     => $hometeam,
                'away team'     => $awayteam,
            );
        } // end foreach

        // clear cache to avoid memory exhausting
        $crawler->clear();
        return;
    }
}
