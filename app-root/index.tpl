<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="app-deploy-target" content="{$deployTarget}" />

	<link rel="apple-touch-icon" href="img/app-icons/57.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="img/app-icons/114.png" />

	<title>AnswerQuest</title>
	
	<script type="text/javascript">
		var AQ_Series = {json_encode(Series::getByHandle($.get.code)->getData())};
	</script>
	
	{loadCSS "application.css"}
	<link href='http://fonts.googleapis.com/css?family=Coustard|Ubuntu:700,400' rel='stylesheet' type='text/css'>
	
	{if $.get.weinre}
		<script src="http://{$.server.HTTP_HOST}:8080/target/target-script-min.js#{$.server.HTTP_HOST}"></script>
	{/if}
	
	{* Load Sencha Touch and patches *}
	{*loadJS "touch/sencha-touch-all-debug.js"*}
	<script src="http://docs.sencha.com/touch/2-0/touch/sencha-touch-all.js"></script>
	
	{loadJS "app.js+controller/*+views/*+models/*+stores/*"}
	
</head>
<body></body>
</html>