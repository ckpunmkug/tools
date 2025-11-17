<?php

class Page
{

	static function header()
	{//{{{//
		
		$html = [];
		$_ = [];
		
		$html[0] =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<header>
	<a href="index.php?page=new_site">New site</a>
	<a href="index.php?page=sites_list">List of sites</a>
	<a href="index.php?page=ai_console">AI console</a>
</header>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body = $html[0].HTML::$body;
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
*
	{
		font-family: Sans;
	}
hr
	{
		background: #888;
		border: none;
		height: 1px;
	}
button, select
	{
		margin: 0.5rem 0;
	}
input[type="text"], input[type="password"]
	{
		width: calc(100% - 1ch);
	}
textarea
	{
		width: calc(100% - 1ch);
		height: 18ch;
	}
iframe
	{
		width: calc(100% - 2px);
		height: 18ch;
		border: solid 1px #333;
	}
label
	{
		line-height: 25px;
	}
div[name="grid"]
	{
		display: grid;
		grid-template-columns: 40ch 40ch 40ch 40ch;
	}
button[class="changer"]
	{
		all: unset;
		display: inline-block;
		border: none;
		padding: 3px;
		cursor: pointer;
	}
button[class="changer"]:hover
	{
		background: #DDD;
	}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		//HTML::$style .= $html[1];
		
		return(true);
		
	}//}}}//

	static function index()
	{//{{{//
		
		$html = [];
		$_ = [];
		
		$html['body'] =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= $html["body"];
		
		return(true);
		
	}//}}}//
	
// Layouts

	static function form_layout(string $legend, string $content)
	{//{{{//
	
		$_ = [
			"csrf_token" => htmlentities(CSRF_TOKEN),
		];
		$html = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<form action="index.php" method="post" enctype="multipart/form-data">
	<input name="csrf_token" value="{$_['csrf_token']}" type="hidden" />
	<fieldset>
		<legend>{$legend}</legend>
{$content}
	</fieldset>
</form>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		return($html);
		
	}//}}}//

	static function tournament_layout(
		int $id,
		string $tournament,
		array $events
	) {//{{{//
	
		$html = [];
		$_ = [];
		
		$html[0] = '<table>';
		$counter = 0;
		foreach($events as $event) {
			
			$counter += 1;
			
			$_["location"] = htmlentities($event["location"]);
			
			if(@strval($event["opponents"][0]) != "" &&  @strval($event["opponents"][1]) != "") {
				$_["opponents"] = htmlentities($event["opponents"][0]).' - '.htmlentities($event["opponents"][1]);
			}
			elseif(@strval($event["opponents"][0]) != "") {
				$_["opponents"] = htmlentities($event["opponents"][0]);
			}
			else continue;
			
			$_["date"] = date('d.m.y', $event["date"]);
			$_["time"] = date('H:i', $event["date"]);
			
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<tr>
	<td>{$counter}</td>
	<td>{$_["opponents"]}</td>
	<td>{$_["location"]}</td>
	<td>{$_["date"]} {$_["time"]}</td>
</tr>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		$html[0] .= '</table>';
		
		$_["id"] = htmlentities($id);
		$_["tournament"] = htmlentities($tournament);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id" value="{$_['id']}" type="hidden" />
<h4>{$_["tournament"]}</h4>
{$html[0]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return($html[1]);
		
	}//}}}//

// Site

	static function site_layout(
		string $url,
		string $user,
		string $password,
		string $title,
		string $description,
		string $keywords
	) {//{{{//
		
		$html = [];
		$_ = [];
		
		$_["url"] = htmlentities($url);
		$_["user"] = htmlentities($user);
		$_["password"] = htmlentities($password);
		$_["title"] = htmlentities($title);
		$_["description"] = htmlentities($description);
		$_["keywords"] = htmlentities($keywords);
		
		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	URL<br/>
	<input name="url" value="{$_["url"]}" type="text" placeholder="https://example.com/admin/index.php" />
</label>
<br/>
<label>
	User<br/>
	<input name="user" value="{$_["user"]}" type="text" placeholder="webmaster" />
</label>
<br/>
<label>
	Password<br/>
	<input name="password" value="{$_["password"]}" type="password" placeholder="qwerty"/>
</label>
<br/>
<label>
	Title<br/>
	<input name="title" value="{$_["title"]}" type="text" />
</label>
<label>
	Description<br/>
	<input name="description" value="{$_["description"]}" type="text" />
</label>

<label>
	Keywords<br/>
	<input name="keywords" value="{$_["keywords"]}" type="text" />
</label>


HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		return($html[0]);
		
	}//}}}//
	
	static function new_site()
	{//{{{//
		
		$html = [];
		$_ = [];
		
		$html[0] = self::site_layout('', '', '', '', '', '');
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$html[0]}
<button name="action" value="add_site" type="submit">Add</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'New site';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//
	
	static function edit_site()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		$site = Data::select_site($id);
		if($site === NULL) return(NULL);
		if(!is_array($site)) {
			trigger_error("Can't select 'site' by 'id'", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$html[0] = self::site_layout(
			$site["url"],
			$site["user"],
			$site["password"],
			$site["title"],
			$site["description"],
			$site["keywords"],
		);
		
		$_["id"] = htmlentities($site["id"]);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id" value="{$_['id']}" type="hidden" />
{$html[0]}
<button name="action" value="change_site" type="submit">Save</button>
<button name="action" value="delete_site" type="submit">Delete</button>
<button name="action" value="get_languages" type="submit">Add tournament</button>
<a href="index.php?page=tournaments_list&site={$_["id"]}">List of tournaments</a>
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'Edit site';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//
	
	static function sites_list()
	{//{{{//
		
		$sites = Data::select_sites();
		if(!is_array($sites)) {
			trigger_error("Can't select 'sites'", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$html[0] = '';
		foreach($sites as $index => $id) {
			$site = Data::select_site($id);
			if(!is_array($site)) {
				trigger_error("Can't select 'site'", E_USER_WARNING);
				return(false);
			}
			
			$_["index"] = htmlentities($index);
			$_["id"] = htmlentities($site["id"]);
			$_["url"] = htmlentities($site["url"]);
			
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<li>
	<input name="id[{$_['index']}]" value="{$_['id']}" type="checkbox" />
	<a href="index.php?page=edit_site&id={$_['id']}">{$_["url"]}</a>
</li>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<ul>
{$html[0]}
</ul>
<button name="action" value="delete_sites" type="submit">Delete</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		HTML::$title = "List of sites";
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//
	
// Event
	
	static function select_language()
	{//{{{/
	
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		$data = cpservm::get_languages($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'languages'", E_USER_WARNING);
			return(false);
		}
		if(!eval(Check::$array.='$data["languages"]')) return(false);
		$languages = $data["languages"];
		
		$html = [];
		$_ = [];
		
		$html[0] = '';
		foreach($languages as $language) {
		
			$_["lng"] = htmlentities($language["lng"]);
			$_["name"] = htmlentities($language["name"]);
			
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<button name="lng" value="{$_['lng']}" type="submit">{$_["name"]}</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$_["id"] = htmlentities($id);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id" value="{$_['id']}" type="hidden" />
<input name="action" value="get_sports" type="hidden" />
{$html[0]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'Select language';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//
	
	static function select_sport()
	{//{{{/
	
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
	
		$data = cpservm::get_sports($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'sports'", E_USER_WARNING);
			return(false);
		}
		if(!eval(Check::$array.='$data["sports"]')) return(false);
		$sports = $data["sports"];
	
		$html = [];
		$_ = [];
		
		$html[0] = '';
		foreach($sports as $sport) {
		
			$_["sportId"] = htmlentities($sport["id"]);
			$_["localized"] = htmlentities($sport["name"]);
			
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<button name="sportId" value="{$_['sportId']}" type="submit">{$_["localized"]}</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$_["id"] = htmlentities($id);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id" value="{$_['id']}" type="hidden" />
<input name="action" value="get_tournaments" type="hidden" />
{$html[0]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'Select sport';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//

	static function select_tournament()
	{//{{{/
	
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
	
		$data = cpservm::get_tournaments($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'tournaments'", E_USER_WARNING);
			return(false);
		}	
		if(!eval(Check::$array.='$data["tournaments"]')) return(false);
		$tournaments = $data["tournaments"];
	
		$html = [];
		$_ = [];
		
		$html[0] = '';
		foreach($tournaments as $tournament) {
		
			$_["tournamentId"] = htmlentities($tournament["id"]);
			$_["localized"] = htmlentities($tournament["name"]);
			
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<button name="tournamentId" value="{$_['tournamentId']}" type="submit">{$_["localized"]}</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$_["id"] = htmlentities($id);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id" value="{$_['id']}" type="hidden" />
<input name="action" value="get_events" type="hidden" />
{$html[0]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'Select tournament';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//

	static function save_events()
	{//{{{/
	
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		if(!eval(Check::$string.='$_GET["tournamentId"]')) return(false);
		$tournamentId = intval($_GET["tournamentId"]);
		
		if(!eval(Check::$string.='$_GET["lng"]')) return(false);
		$lng = $_GET["lng"];
	
		$data = cpservm::get_events($id);
		if(!is_array($data)) {
			trigger_error("Can't get 'events'", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$string.='$data["tournament"]')) return(false);
		$tournament = $data["tournament"];
		
		if(!eval(Check::$array.='$data["events"]')) return(false);
		$events = $data["events"];
	
		$html = [];
		$_ = [];
		
		$html[0] = self::tournament_layout($id, $tournament, $events);
		
		$_["tournamentId"] = htmlentities($tournamentId);
		$_["lng"] = htmlentities($lng);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
{$html[0]}
<br />
<input name="tournamentId" value="{$_["tournamentId"]}" type="hidden" />
<input name="lng" value="{$_["lng"]}" type="hidden" />
<button name="action" value="add_tournament" type="submit">Add to site</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'Save events';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		return(true);
		
	}//}}}//

	static function view_tournament()
	{//{{{//
		
		$html = [];
		$_ = [];
		
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$site = intval($_GET["id"]);
		
		$tournament = Data::select_tournament($site);
		if(!is_array($tournament)) {
			trigger_error("Can't select 'tournament'", E_USER_WARNING);
			return(false);
		}
		
		$array = Data::select_events($tournament["id"]);
		if(!is_array($array)) {
			trigger_error("Can't select 'events'", E_USER_WARNING);
			return(false);
		}
		
		$events = [];
		foreach($array as $id) {
			$event = Data::select_event($id);
			if(!is_array($event)) {
				trigger_error("Can't select 'event'", E_USER_WARNING);
				return(false);
			}
			array_push($events, $event);
		}
		
		$html = [];
		$_ = [];
		
		$_["name"] = htmlentities($tournament["name"]);
		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h4>{$_["name"]}</h4>
<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		foreach($events as $event) {
			$_['event'] = ($event["id"]);
			$_['date'] = date('d.m.y', $event["date"]); 
			
			if(@strval($event["opponents"][0]) != "" &&  @strval($event["opponents"][1]) != "") {
				$_["opponents"] = htmlentities($event["opponents"][0]).' - '.htmlentities($event["opponents"][1]);
			}
			elseif(@strval($event["opponents"][0]) != "") {
				$_["opponents"] = htmlentities($event["opponents"][0]);
			}
			else continue;
	
			
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="index.php?page=view_event&id={$_['event']}">{$_['date']} - {$_["opponents"]}</a>
<br /><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		
		$_["site"] = htmlentities($tournament["site"]);
		$_["id"] = htmlentities($tournament["id"]);
		
		$_["title"] = htmlentities($tournament["name"]);
		$_["description"] = htmlentities($tournament["description"]);
		$_["keywords"] = htmlentities($tournament["keywords"]);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="site" value="{$_["site"]}" type="hidden" />
<input name="id" value="{$_["id"]}" type="hidden" />
{$html[0]}

<p>
На странице в интернете будет размещён список прогнозов для следующего чемпионата: {$_["title"]}.<br />
Не используй форматирование. Не используй эмодзи. Дай descriptions и keywords для такой страницы.
</p>

<label>
	Description<br/>
	<input name="description" value="{$_["description"]}" type="text" />
</label>

<label>
	Keywords<br/>
	<input name="keywords" value="{$_["keywords"]}" type="text" />
</label>
<button name="action" value="change_tournament" type="submit">Save</button>
<button name="action" value="delete_tournament" type="submit">Delete</button>
<a href="index.php?page=edit_site&id={$_["site"]}">Back to site</a>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'View tournament';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		$_["csrf_token"] = htmlentities(CSRF_TOKEN);
		$_["tournament"] = htmlentities($id);
		
		$default_prompt = 
///////////////////////////////////////////////////////////////{{{//
<<<'HEREDOC'
"%tournament%" Встречаются "%opponent1%" и "%opponent2%" встреча будет проходить в "%location%" матч состоится %date%. Дай прогноз на эту встречу. Не используй форматирование. Не используй эмодзи. Оставь только: анализ команд, ключевые факторы, прогноз, вероятность счёта, тотал. Используй для вывода json. анализ команд сохрани в teams_analysis, ключевые факторы сохрани в key_factors, прогноз сохрани в prediction, вероятность счёта сохрани в probability_count, тотал сохрани в total. В "вероятность счёта" сохрани только наиболее вероятный, оставь только цифры и двоеточие, позиции команд как переданы в запросе. В "тотал" используй только цифры и < > <= >=

Представь что данные размещены на странице в интернете. Добавь для такой страници (в json) ключевые слова (keywords) и описание (description)
В ответе оставь только json.
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		$_["default_prompt"] = htmlentities($default_prompt);
		
		$html[3] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<form action="index.php" method="post" target="io" enctype="multipart/form-data">
	<input name="csrf_token" value="{$_['csrf_token']}" type="hidden" />
	<input name="tournament" value="{$_['tournament']}" type="hidden" />
	<fieldset>
		<legend>Generate predictions</legend>
		<p>
Use the following tags for auto-replacement: %tournament% , %opponent1% , %opponent2%, %location% , %date%
		</p>
		<label>
			Prediction prompt<br />
			<textarea name="prompt">{$_["default_prompt"]}</textarea>
		</label>
		<button name="action" value="get_predictions">Generate</button>
	</fieldset>
</form>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= $html[3];
		
		$html[4] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
	<span class="legend">Progress</span><br />
	<iframe name="io"></iframe>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= $html[4];
		
		return(true);
	
		
	}//}}}//
	
	static function tournaments_list()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["site"]')) return(false);
		$site = intval($_GET["site"]);
		
		$tournaments = Data::select_tournaments($site);
		if(!is_array($tournaments)) {
			trigger_error("Can't select 'tournaments'", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$html[0] = '';
		foreach($tournaments as $index => $id) {
			$tournament = Data::select_tournament($id);
			if(!is_array($tournament)) {
				trigger_error("Can't select 'tournament'", E_USER_WARNING);
				return(false);
			}
		
			$_["index"] = htmlentities($index);
			$_["id"] = htmlentities($tournament["id"]);
			$_["name"] = htmlentities($tournament["name"]);
			$html[0] .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id[{$_['index']}]" value="{$_['id']}" type="checkbox" />
<a href="index.php?page=view_tournament&id={$_['id']}">{$_["name"]}</a>
<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$_["site"] = htmlentities($site);
		
		$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="site" value="{$_['site']}" type="hidden" />
{$html[0]}
<button name="action" value="delete_tournaments" type="submit">Delete</button>
<a href="index.php?page=edit_site&id={$_["site"]}">Back to site</a>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'List of tournaments';
		HTML::$body .= self::form_layout(HTML::$title, $html[1]);
		return(true);
		
	}//}}}//

	static function view_event()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		$event = Data::select_event($id);
		if($event === NULL) return(NULL);
		if(!is_array($event)) {
			trigger_error("Can't select 'event'", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$_["location"] = htmlentities($event["location"]);
		$_["opponent1"] = htmlentities($event["opponents"][0]);
		$_["opponent2"] = htmlentities($event["opponents"][1]);
		$_["date"] = date('d.m.y', $event["date"]);
		$_["time"] = date('H:i', $event["date"]);
		
		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h4>Место проведения</h4>
{$_["location"]}
<h4>Играют</h4>
{$_["opponent1"]} - {$_["opponent2"]}
<h4>Время проведения</h4>
{$_["date"]} {$_["time"]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		$prediction = Data::select_prediction($event["id"]);
		if($prediction === false) {
			trigger_error("Can't select 'prediction'", E_USER_WARNING);
			return(false);
		}
		$prediction = @json_decode($prediction["prediction"], true);
		
		$html[1] = '';
		if(is_array($prediction)) {
			$_["teams_analysis"] = htmlentities($prediction["teams_analysis"]);
			$_["key_factors"] = htmlentities($prediction["key_factors"]);
			$_["prediction"] = htmlentities($prediction["prediction"]);
			$_["probability_count"] = htmlentities($prediction["probability_count"]);
			$_["total"] = htmlentities($prediction["total"]);
			$_["keywords"] = htmlentities($prediction["keywords"]);
			$_["description"] = htmlentities($prediction["description"]);
			
			$html[1] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h4>Анализ команд</h4>
{$_["teams_analysis"]}
<h4>Ключевые факторы</h4>
{$_["key_factors"]}
<h4>Прогноз</h4>
{$_["prediction"]}
<h4>Выроятный счёт</h4>
{$_["probability_count"]}
<h4>Тотал</h4>
{$_["total"]}
<h4>Ключевые слова</h4>
{$_["keywords"]}
<h4>Описание</h4>
{$_["description"]}

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$_["tournament"] = htmlentities($event["tournament"]);
		
		$html[2] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<form>
	<fieldset>
{$html[0]}
{$html[1]}
	<hr />
	<a href="index.php?page=view_tournament&id={$_["tournament"]}">Back to tournament</a>
	<br />	<br />

	</fieldset>
</form>
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = "Event viewer";
		HTML::$body = $html[2];
		
		return(true);
		
	}//}}}//

	// AI
	
	static function ai_console()
	{//{{{//
		
		$html = [];
		$_ = [];
	
		$_["csrf_token"] = htmlentities(CSRF_TOKEN);
	
		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<main>
	<form action="index.php" method="post" target="request" enctype="multipart/form-data">
		<input name="csrf_token" value="{$_['csrf_token']}" type="hidden" />
		<input name="action" value="ai_request" type="hidden" />
		<fieldset>
			<legend>AI prompt</legend>
			<textarea name="request" autofocus></textarea>
			<button type="submit">Send</button>
		</fieldset>
	</form>
		<fieldset>
			<legend>AI answer</legend>
			<iframe name="request" width="100%" height="100%"></iframe>
		</fieldset>
</main>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$title = 'AI Console';
		HTML::$body .= $html[0];
		
		$html[1] = 
///////////////////////////////////////////////////////////////
<<<'HEREDOC'

var form = null;
var iframe = null;
var textarea = null;
var div = null;

function toProgress()
{//{{{//
	document.body.style.setProperty("cursor", "progress");
	div = document.createElement("div");
	div.style.setProperty("background", "black");
	div.style.setProperty("position", "absolute");
	div.style.setProperty("top", "0");
	div.style.setProperty("left", "0");
	div.style.setProperty("width", "100%");
	div.style.setProperty("height", "100%");
	div.innerHTML="Waiting for a answer from AI";
	div = document.body.appendChild(div);
}//}}}//

function toComplete()
{//{{{//
	div.remove();
}//}}}//

function windowOnLoad(event)
{//{{{//
	
	form = document.querySelector("form");
	form.addEventListener("submit", function(event) {
		toProgress();
	});
	
	iframe = document.querySelector("iframe");
	iframe.addEventListener("load", function(event) {
		toComplete();
	});
	
	textarea = document.querySelector("textarea");
	textarea.addEventListener("keydown", function(event) {
		if(event.key == "Enter") {
			if(event.ctrlKey == true) {
				toProgress();
				form.submit();
			}
		}
	});

}//}}}//

window.addEventListener('load', windowOnLoad)

HEREDOC;
///////////////////////////////////////////////////////////////
		
		HTML::$script .= $html[1];
		
		return(true);
		
	}//}}}//

	static function get_prediction()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["job"]')) return(false);
		$job = intval($_GET["job"]);
		
		$task = Job::select_task($job);
		if($task === NULL) {
			header("Location: index.php?page=job_controller&job={$job}");
			exit(0);
		}
		if(!is_array($task)) {
			trigger_error("Can't select task", E_USER_WARNING);
			return(false);
		}
		
		if(!eval(Check::$int.='$task["parameters"]["event"]')) return(false);
		$event = $task["parameters"]["event"];
		
		$return = Action::get_prediction($event);
		if(!$return) {
			Job::update_task_state($task["id"], 2);
			trigger_error("Can't get prediction", E_USER_WARNING);
			return(false);
		}
		Job::update_task_state($task["id"], 3);
		
		header("Location: index.php?page=job_controller&job={$job}");
		
		exit(0);
		
	}//}}}//

// Job /////////////////////////////////////////////////////////////////////////
	
	static function job_controller()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["job"]')) return(false);
		$job = intval($_GET["job"]);
		
		$task = Job::select_task($job);
		if($task === NULL) {
			HTML::$body .= "Complete";
			exit(0);
		}
		if(!is_array($task)) {
			trigger_error("Can't get 'task'", E_USER_WARNING);
			return(false);
		}
		
		$_ = [];
		
		if(!eval(Check::$string.='$task["parameters"]["action"]')) return(false);
		$_["job"] = htmlentities($job);
		$_["action"] = htmlentities($task["parameters"]["action"]);
		HTML::$meta .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<meta http-equiv="Refresh" content="0; URL=index.php?page={$_["action"]}&job={$_['job']}" />
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		if(!eval(Check::$string.='$task["parameters"]["about"]')) return(false);
		$_["about"] = htmlentities($task["parameters"]["about"]);
		HTML::$body = $_["about"];
		
		exit(0);
		
	}//}}}//

}

