<?php

namespace Exceptions;

use Throwable;

class ExceptionHandler {
	/**
	 * @param \Throwable $t
	 */
	public static function handle(Throwable $t) {
		$message = $t->getMessage();
		$code = " [" . $t->getCode() . "]";

		if ($code == 0)
			$code = "";

		$file = $t->getFile();
		$line = $t->getLine();
		$trace = $t->getTraceAsString();

		if (self::is_leveled($t)) {
			/** @noinspection PhpUndefinedMethodInspection */
			if ($t->getLevel() > LeveledException::LEVEL_INFO)
				http_response_code(500);

			/** @noinspection PhpUndefinedMethodInspection */
			$level = LeveledException::stringifyLevel($t->getLevel());
		}

		ob_start();
		var_dump($t);
		$dump = ob_get_clean();

		echo <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	    <title>Error</title>
	
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	    <link rel="stylesheet" href="/css/style.css">
    </head>
	<body>
	    <div class="container mb-3">
	        <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
			    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			        <span class="navbar-toggler-icon"></span>
			    </button>
			
			    <div class="collapse navbar-collapse" id="navbarSupportedContent">
			        <ul class="navbar-nav mr-auto">
			            <li class="nav-item active">
			                <!--suppress HtmlUnknownTarget -->
							<a class="nav-link" href="/home">Home</a>
			            </li>
			        </ul>
			    </div>
			</nav>
	    </div>
	
	    <div class="container">
	    	<h3 class="mb-3"><small>An exception occurred:</small>$code $message</h3>
	    	
	    	<table class="table table-bordered">
	    		<tr>
	    			<td>File</td>
	    			<td>$file</td>
				</tr>
	    		<tr>
	    			<td>Line</td>
	    			<td>$line</td>
				</tr>
HTML;

		if (isset($level))
			echo <<<HTML
	    		<tr>
	    			<td>Level</td>
	    			<td>$level</td>
				</tr>
HTML;

		echo <<<HTML
				<tr>
					<td>Trace</td>
					<td><pre style="max-height: 200px; overflow: auto">$trace</pre></td>
				</tr>
				<tr>
					<td>Dump</td>
					<td><pre style="max-height: 300px; overflow: auto">$dump</pre></td>
				</tr>
			</table>
	    </div>
	
		<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	</body>
</html>
HTML;

		/** @noinspection PhpUndefinedMethodInspection */
		if (self::is_leveled($t) && $t->getLevel() > LeveledException::LEVEL_INFO)
			die;
	}

	private static function is_leveled(Throwable $t) {
		return get_class($t) == LeveledException::class;
	}
}