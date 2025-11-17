<?php

class Page
{
	static $predictions_per_page = CONFIG["predictions_per_page"];

	static function short_event_layout(int $id)
	{//{{{//
		
		$event = Data::select_event($id);
		if(!is_array($event)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return('');
		}
		
		$tournament = Data::select_tournament($event["tournament"]);
		if(!is_array($tournament)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$prediction = Data::select_prediction($event["id"]);
		if(!is_array($prediction)) {
			trigger_error("Can't select 'prediction'", E_USER_WARNING);
			return('');
		}
		if(!eval(Check::$string.='$prediction["prediction"]')) return('');
		if(!eval(Check::$string.='$prediction["probability_count"]')) return('');
		if(!eval(Check::$string.='$prediction["total"]')) return('');
		
		$html = [];
		$_ = [];
		
		$_["opponent_1"] = htmlentities($event["opponents"][0]);
		$_["opponent_2"] = htmlentities($event["opponents"][1]);
		$_["date"] = date('G:i j.n.y', ($event["date"] + 10800));
		$_["prediction"] = htmlentities($prediction["prediction"]);
		$_["probability_count"] = htmlentities($prediction["probability_count"]);
		$_["total"] = htmlentities($prediction["total"]);
		
		$_["event"] = htmlentities($event["translit"]);
		$_["tournament"] = htmlentities($tournament["translit"]);

		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div class="short_prediction">
	<h2>
		<span class="opponent1">{$_["opponent_1"]}</span>
		против
		<span class="opponent2">{$_["opponent_2"]}</span>
	</h2>
	<span class="prediction">{$_["prediction"]}</span><br />
	Возможный счёт:
	<span class="probability_count">{$_["probability_count"]}</span>
	Тотал:
	<span class="total">{$_["total"]}</span>
	<br />
	Начало в 	
	<span class="date">{$_["date"]}</span>
	время московское
	<br />
	<a class="details" href="index.php?tournament={$_["tournament"]}&event={$_["event"]}">Подробнее</a>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return($html[0]);
		
	}//}}}//

	static function tournaments_layout()
	{//{{{//
		$html = [];
		$_ = [];
		
		$tournaments = Data::select_tournaments();
		if(!is_array($tournaments)) {
			trigger_error("Can't select 'tournaments'", E_USER_WARNING);
			return(false);
		}
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div class="tournaments">
	<a href="/">Главная</a>
	<br />
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		foreach($tournaments as $id) {
			$tournament = Data::select_tournament($id);
			if(!is_array($tournament)) {
				trigger_error("Can't select 'tournament'", E_USER_WARNING);
				return(false);	
			}
			
			$_['translit'] = htmlentities($tournament["translit"]);
			$_['title'] = htmlentities($tournament["title"]);
			
			$html[1] .=
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a class="tournament" href="index.php?tournament={$_['translit']}">{$_["title"]}</a>
<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		$html[1] .= '</div>'."\n";
		
		return($html[1]);
		
	}//}}}//

	static function page_numbers_layout(int $current, int $count, string $appendix = '')
	{//{{{//
		
		$html = [];
		$_ = [];
		
		$html[0] = '';
		if($current > 1) {
			$_["previous"] = strval($current - 1);
			$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a class="previous" href="index.php?page={$_["previous"]}{$appendix}">Предыдущая</a>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$html[1] = '';
		if($current < $count) {
			$_["next"] = strval($current + 1);
			$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a class="next" href="index.php?page={$_["next"]}{$appendix}">Следующая</a>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$html[2] = '';
		for($number = 1; $number <= $count; $number += 1) {
			$_["number"] = strval($number);
			if($number == $current) {
				$_["current"] = ' id="current"';
			}
			else {
				$_["current"] = '';
			}
			$html[2] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a{$_["current"]} class="page_number" href="index.php?page={$_["number"]}{$appendix}">{$_["number"]}</a>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$html[3] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<br />
{$html[0]}{$html[2]}{$html[1]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		return($html[3]);
		
	}//}}}//

	static function index(int $page)
	{//{{{//
		
		$pages_count = self::get_index_pages_count();
		if(!is_int($pages_count)) {
			trigger_error("Can't get 'index_pages' count", E_USER_WARNING);
			return(false);
		}
		
		if(!($page >= 1 && $page <= $pages_count)) return(0);
		
		$events = Data::select_all_events();
		if(!is_array($events)) {
			trigger_error("Can't select all 'events'", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$html[1] = self::tournaments_layout();
		$html[1] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h1>Все прогнозы</h1>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		// Events
		
		$count = count($events);
		$begin = ($page - 1) * self::$predictions_per_page;
		$end = $begin + self::$predictions_per_page;
		
		if($end > $count) $end = $count;
		
		$html[0] = '';
		for($index = $begin; $index < $end; $index += 1) {
			$html[0] .= self::short_event_layout($events[$index]);
		}
		
		HTML::$body .= $html[1].$html[0];
		
		// Site 
		
		$site = Data::select_site();
		if(!is_array($site)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		
		HTML::$title = $site["title"];
		HTML::$description = $site["description"];
		HTML::$keywords = $site["keywords"];
		
		HTML::$body .= self::page_numbers_layout($page, $pages_count);
		
		return(true);
		
	}//}}}//
	
	static function get_index_pages_count()
	{//{{{//
		
		$events_count = Data::select_all_events_count();
		if(!is_int($events_count)) {
			trigger_error("Can't select all 'events' count", E_USER_WARNING);
			return(false);
		}
		
		$remainder = $events_count % self::$predictions_per_page;
		$quotient = ($events_count - $remainder) / self::$predictions_per_page;
		
		$pages_count = $quotient;
		if($remainder > 0) $pages_count += 1;
		
		return($pages_count);
		
	}//}}}//

	static function tournament(string $translit, int $page)
	{//{{{//
		
		$tournament = Data::select_tournament_by_translit($translit);
		
		$pages_count = self::get_tournament_pages_count($tournament["id"]);
		if(!is_int($pages_count)) {
			trigger_error("Can't get 'tournament_pages' count", E_USER_WARNING);
			return(false);
		}
		
		if(!($page >= 1 && $page <= $pages_count)) return(NULL);
		
		$events = Data::select_events($tournament["id"]);
		if(!is_array($events)) {
			trigger_error("Can't select all 'events'", E_USER_WARNING);
			return(false);
		}
		
		// Events
		
		$count = count($events);
		$begin = ($page - 1) * self::$predictions_per_page;
		$end = $begin + self::$predictions_per_page;
		
		if($end > $count) $end = $count;
		
		$html = [];
		$_ = [];
		
		$html[0] = self::tournaments_layout();
		
		$_['h3'] = htmlentities($tournament["title"]);
		$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h1>{$_['h3']}</h1>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		for($index = $begin; $index < $end; $index += 1) {
			$html[0] .= self::short_event_layout($events[$index]);
		}
		
		HTML::$body .= $html[0];
		
		HTML::$title = $tournament["title"];
		HTML::$description = $tournament["description"];
		HTML::$keywords = $tournament["keywords"];
		
		$appendix = '&tournament='.htmlentities($tournament["translit"]);
		HTML::$body .= self::page_numbers_layout($page, $pages_count, $appendix);
		
		return(true);
		
	}//}}}//
	
	static function get_tournament_pages_count(int $tournament)
	{//{{{//
		
		$events_count = Data::select_tournament_events_count($tournament);
		if(!is_int($events_count)) {
			trigger_error("Can't select tournament 'events' count", E_USER_WARNING);
			return(false);
		}
		
		$remainder = $events_count % self::$predictions_per_page;
		$quotient = ($events_count - $remainder) / self::$predictions_per_page;
		
		$pages_count = $quotient;
		if($remainder > 0) $pages_count += 1;
		
		return($pages_count);
		
	}//}}}//

	static function event(string $tournament, string $event)
	{//{{{//
		
		$tournament = Data::select_tournament_by_translit($tournament);
		if($tournament === NULL) return(NULL);
		if(!is_array($tournament)) {
			trigger_error("Can't select 'tournament' by translit", E_USER_WARNING);
			return(false);
		}
		
		$event = Data::select_event_by_translit($tournament["id"], $event);
		if(!is_array($tournament)) {
			trigger_error("Can't select 'event' by translit", E_USER_WARNING);
			return(false);
		}
		
		$prediction = Data::select_prediction($event["id"]);
		if(!is_array($prediction)) {
			trigger_error("Can't select 'prediction'", E_USER_WARNING);
			return(false);
		}
		if(!eval(Check::$string.='$prediction["teams_analysis"]')) return('');
		if(!eval(Check::$string.='$prediction["key_factors"]')) return('');
		if(!eval(Check::$string.='$prediction["prediction"]')) return('');
		if(!eval(Check::$string.='$prediction["probability_count"]')) return('');
		if(!eval(Check::$string.='$prediction["total"]')) return('');
		
		$html = [];
		$_ = [];
		
		$_["translit"] = htmlentities($tournament["translit"]);
		
		$_["title"] = htmlentities($event["title"]);
		$_["description"] = htmlentities($event["description"]);
		$_["keywords"] = htmlentities($event["keywords"]);
		
		$_["tournament"] = htmlentities($tournament["title"]);
		$_["location"] = htmlentities($event["location"]);
		$_["opponent_1"] = htmlentities($event["opponents"][0]);
		$_["opponent_2"] = htmlentities($event["opponents"][1]);
		$_["date"] = date('G:i j.n.y', ($event["date"] + 10800));
		
		$_["teams_analysis"] = htmlentities($prediction["teams_analysis"]);
		$_["key_factors"] = htmlentities($prediction["key_factors"]);
		$_["prediction"] = htmlentities($prediction["prediction"]);
		$_["probability_count"] = htmlentities($prediction["probability_count"]);
		$_["total"] = htmlentities($prediction["total"]);
		
		$html[0] = self::tournaments_layout();
		
		$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<div class="prediction">
	<h1>
		<span class="opponent1">{$_["opponent_1"]}</span>
		против
		<span class="opponent2">{$_["opponent_2"]}</span>
		прогноз на {$_["date"]}
	</h1>
	<h2>{$_["tournament"]}</h2>
	<span class="prediction">{$_["prediction"]}</span><br />
	Возможный счёт:
	<span class="probability_count">{$_["probability_count"]}</span>
	Тотал:
	<span class="total">{$_["total"]}</span>
	<br />
	Начало в 	
	<span class="date">{$_["date"]}</span>
	время московское
	<br />
	<h3>Анализ команд</h3>
	<span class="teams_analysis">{$_["teams_analysis"]}</span>
	<h3>Ключевые факторы</h3>
	<span class="key_factors">{$_["key_factors"]}</span>
</div>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = $_["title"].". Возможный счёт - {$_["probability_count"]}";
		HTML::$description = $_["description"];
		HTML::$keywords = $_["keywords"];
		HTML::$body .= $html[0];
		
		return(true);
		
	}//}}}//
	
}

