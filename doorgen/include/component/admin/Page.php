<?php

class Page
{

	function __construct()
	{//{{{//

		$style =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
*
	{
		font-family: Sans;
		font-size: 14px;
	}
a, button
	{
		margin: 5px 0px;
	}
input[type="text"]
	{
		width: calc(100% - 1ch);
	}
textarea
	{
		width: calc(100% - 1ch);
		height: 4em;
	}
label
	{
		line-height: 25px;
	}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
		$body = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="index.php?page=edit_site"
	><button>Edit site</button></a>
	
<a href="index.php?page=new_category"
	><button>New category</button></a>

<a href="index.php?page=categories_list"
	><button>List of categories</button></a>

<a href="index.php?page=new_article"
	><button>New article</button></a>

<a href="index.php?page=articles_list"
	><button>List of articles</button></a>

<a href="index.php?page=create_tables"
	><button>Create tables</button></a>
<hr />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
	}//}}}//
	
	function index()
	{//{{{//
		
		HTML::$title = 'Index - main page';
		HTML::$body .= $this->form_template('Empty', '');
		
		return(true);
		
	}//}}}//
	
	function form_template(string $legend, string $content)
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
		
	function edit_site()
	{//{{{//
	
		$site = Data::select_site();
		if(!is_array($site)) {
			trigger_error("Can't select 'site'", E_USER_WARNING);
			return(false);
		}
		
		$html = [];
		$_ = [];
		
		$_["title"] = htmlentities($site["title"]);
		$_["description"] = htmlentities($site["description"]);
		$_["keywords"] = htmlentities($site["keywords"]);
		
		$html[0] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	Title<br />
	<input name="title" value="{$_['title']}" type="text" />
</label>
<label>
	Description<br />
	<input name="description" value="{$_['description']}" type="text" />
</label>
<label>
	Keywords<br />
	<input name="keywords" value="{$_['keywords']}" type="text" />
</label>
<br />
<button name="action" value="set_site" type="submit">Save</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		HTML::$title = 'Edit site';
		HTML::$body .= $this->form_template(HTML::$title, $html[0]);
		return(true);
		
	}//}}}//
		
	function new_category()
	{//{{{//
		
		$legend = 'Create new category';
		$content = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	Id<br />
	<input name="id" type="text" />
</label>
<label>
	Name<br />
	<input name="name" type="text" />
</label>
<label>
	Description<br />
	<input name="description" type="text" />
</label>
<label>
	Keywords<br />
	<input name="keywords" type="text" />
</label>
<br />
<button name="action" value="new_category" type="submit">New</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		HTML::$title = $legend;
		HTML::$body .= $this->form_template($legend, $content);
		return(true);
		
	}//}}}//
		
	function edit_category()
	{//{{{//
	
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		$category = Data::select_category($id);
		if(!is_array($category)) {
			trigger_error("Can't select 'category' from database", E_USER_WARNING);
			return(false);
		}
		
		$legend = 'Edit category';
		
		$_ = [
			"id" => htmlentities($category["id"]),
			"name" => htmlentities($category["name"]),
			"title" => htmlentities($category["title"]),
			"description" => htmlentities($category["description"]),
			"keywords" => htmlentities($category["keywords"]),
			"header" => htmlentities($category["header"]),
		];
		
		$content = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	id<br />
	<input name="id" value="{$_['id']}" type="text" placeholder="0" readonly/>
</label>
<label>
	name [0-9a-zA-Z_-]+<br />
	<input name="name" value="{$_['name']}" type="text" placeholder="category_name-123"/>
</label>
<br />
<label>
	title &lt;title&gt;<br />
	<input name="title" value="{$_['title']}" type="text" placeholder="Название страницы"/>
</label>
<br />
<label>
	description &lt;meta name="description"&gt;<br />
	<textarea name="description" type="text" placeholder="Описание содержимого страницы">{$_['description']}</textarea>
</label>
<br />
<label>
	keywords &lt;meta name="keywords"&gt;<br />
	<input name="keywords" value="{$_['keywords']}" type="text" placeholder="Ключевые фразы у страницы"/>
</label>
<br />
<label>
	header &lt;h1&gt;<br />
	<input name="header" value="{$_['header']}" type="text" placeholder="Главный заголовок на странице"/>
</label>
<br />

<br />
<button name="action" value="update_category" type="submit">Save</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		HTML::$title = $legend;
		HTML::$body .= $this->form_template($legend, $content);
		return(true);
		
	}//}}}//
	
	function categories_list()
	{//{{{//
		
		$categories = Data::select_categories();
		if(!is_array($categories)) {
			trigger_error("Can't select 'categories' from database", E_USER_WARNING);
			return(false);
		}
		
		$legend = 'List of categories';
		
		$content = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<button name="action" value="delete_categories" type="Submit">Delete</button>
<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		foreach($categories as $index => $category) {
			$_ = [
				"index" => htmlentities($index),
				"id" => htmlentities($category["id"]),
				"name" => htmlentities($category["name"]),
				"title" => htmlentities($category["title"]),
			];
			$content .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id[{$_['index']}]" value="{$_["id"]}" type="checkbox" />
<a href="index.php?page=edit_category&id={$_['id']}">{$_["name"]} - {$_["title"]}</a>
<br /> 

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		HTML::$title = $legend;
		HTML::$body .= $this->form_template($legend, $content);
		return(true);
		
	}//}}}//

	function new_article()
	{//{{{//
		
		$legend = 'Create new article';
		
		$categories = Data::select_categories();
		if(!is_array($categories)) {
			trigger_error("Can't select 'categories' from database", E_USER_WARNING);
			return(false);
		}
		
		if(count($categories) == 0) {
			HTML::$title = $legend;
			HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h1>There are no categories</h1>
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
			return(true);
		}
		
		$options = '';
		foreach($categories as $category) {
			$_ = [
				"id" => htmlentities($category["id"]),
				"title" => htmlentities($category["title"]),
			];
			$options .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<option value="{$_['id']}">{$_["title"]}</option>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$content = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	Category<br />
	<select name="category">
{$options}
	</select>
</label>
<br />
<label>
	Title<br />
	<input name="title" type="text" />
</label>
<br />
<label>
	Description<br />
	<textarea name="description" type="text"></textarea>
</label>
<br />
<label>
	Keywords<br />
	<input name="keywords" type="text" />
</label>
<br />
<label>
	Content<br />
	<textarea name="content" type="text"></textarea>
</label>
<br />

<br />
<button name="action" value="new_article" type="submit">New</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		HTML::$title = $legend;
		HTML::$body .= $this->form_template($legend, $content);
		return(true);
		
	}//}}}//

	function edit_article()
	{//{{{//
		
		if(!eval(Check::$string.='$_GET["id"]')) return(false);
		$id = intval($_GET["id"]);
		
		$categories = Data::select_categories();
		if(!is_array($categories)) {
			trigger_error("Can't select 'categories' from database", E_USER_WARNING);
			return(false);
		}
	
		$article = Data::select_article($id);
		if(!is_array($article)) {
			trigger_error("Can't select 'article' from database", E_USER_WARNING);
			return(false);
		}
		
		$category_options = '';
		foreach($categories as $category) {
			$_ = [
				"selected" => '',
				"id" => htmlentities($category["id"]),
				"title" => htmlentities($category["title"]),
			];
			if($article["category"] == $category["id"]) {
				$_["selected"] = ' selected';
			}
			$category_options .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<option value="{$_['id']}"{$_["selected"]}>{$_["title"]}</option>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
	
		$match = Data::select_match($article["id"]);
		if(!is_array($match)) {
			trigger_error("Can't select 'match' from database", E_USER_WARNING);
			return(false);
		}
		
		$match_options = '';
		$STATUS = MATCH_STATUSES;
		foreach($STATUS as $status) {
			$_ = [
				"selected" => '',
				"status" => htmlentities($status),
			];
			if($match["status"] == $status) {
				$_["selected"] = ' selected';
			}
			$match_options .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<option value="{$_['status']}"{$_["selected"]}>{$_['status']}</option>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		$legend = 'Edit article';
		$_ = [
			"article" => [
				"id" => htmlentities($article["id"]),
				"name" => htmlentities($article["name"]),
				"title" => htmlentities($article["title"]),
				"description" => htmlentities($article["description"]),
				"keywords" => htmlentities($article["keywords"]),
				"header" => htmlentities($article["header"]),
				"content" => htmlentities($article["content"]),
			],
			"match" => [
				"id" => htmlentities($match["id"]),
				"score" => htmlentities($match["score"]),
			],
		];
		
		$content = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<label>
	id<br />
	<input name="article[id]" value="{$_['article']['id']}" type="text" placeholder="0" readonly />
</label>
<br />
<label>
	name [0-9a-zA-Z_-]+<br />
	<input name="article[name]" value="{$_['article']['name']}" type="text" placeholder="category_name-123" />
</label>
<br />
<label>
	category<br />
	<select name="article[category]">
{$category_options}
	</select>
</label>
<br />
<label>
	title &lt;title&gt;<br />
	<input name="article[title]" value="{$_['article']['title']}" type="text" placeholder="Название страницы" />
</label>
<br />
<label>
	description &lt;meta name="description"&gt;<br />
	<textarea name="article[description]" type="text" placeholder="Описание содержимого страницы">{$_['article']['description']}</textarea>
</label>
<br />
<label>
	keywords &lt;meta name="keywords"&gt;<br />
	<input name="article[keywords]" value="{$_['article']['keywords']}" type="text" placeholder="Ключевые фразы у страницы" />
</label>
<br />
<label>
	header &lt;h1&gt;<br />
	<input name="article[header]" value="{$_['article']['header']}" type="text" placeholder="Главный заголовок на странице" />
</label>
<br />
<label>
	content<br />
	<textarea name="article[content]" type="text" placeholder="Содержимое страницы">{$_['article']['content']}</textarea>
</label>
<br />
<h4>Match</h4>
<label>
	id<br />
	<input name="match[id]" value="{$_['match']['id']}" type="text" placeholder="0" readonly />
</label>
<br />
<select name="match[status]">
{$match_options}
</select>
<br />
<label>
	score<br />
	<input name="match[score]" value="{$_['match']['score']}" type="text" placeholder="0:0" />
</label>

<br />
<button name="action" value="update_article" type="submit">Save</button>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		HTML::$title = $legend;
		HTML::$body .= $this->form_template($legend, $content);
		return(true);
		
	}//}}}//
	
	function articles_list()
	{//{{{//
		
		$articles = Data::select_articles();
		if(!is_array($articles)) {
			trigger_error("Can't select 'articles' from database", E_USER_WARNING);
			return(false);
		}
		
		$legend = 'List of articles';
		
		$content = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<button name="action" value="delete_article" type="Submit">Delete</button>
<br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		foreach($articles as $index => $article) {
			$_ = [
				"index" => htmlentities($index),
				"id" => htmlentities($article["id"]),
				"name" => htmlentities($article["name"]),
				"title" => htmlentities($article["title"]),
			];
			$content .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<input name="id[{$_['index']}]" value="{$_["id"]}" type="checkbox" />
<a href="index.php?page=edit_article&id={$_['id']}">{$_["name"]} - {$_["title"]}</a>
<br /> 

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		
		HTML::$title = $legend;
		HTML::$body .= $this->form_template($legend, $content);
		return(true);
		
	}//}}}//
	
	function create_tables()
	{//{{{//
		
		$html = [];
		
		$return = Data::create_tables();
		if(!$return) {
			trigger_error("Can't create tables in database", E_USER_WARNING);
			return(false);
		}
		
		$html["body"] = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h1>Tables created</h1>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
		HTML::$body .= $html["body"];
		
		return(true);
		
	}//}}}//
	
}

