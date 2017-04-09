<?php
use App\Route;
use App\Template;
use Exceptions\ExceptionHandler;

include("classes/load.php");

try {
	Route::get(
		"/home",
		"home",
		array(
			'/',
		    '/index.php'
		)
	);

	Route::post("/login", "SessionController@create");
	Route::post("/register", "UserController@create");

	Route::set404(function() {
		Template::display("app.error");
		//Route::redirect("/home");
	});

	Route::handle();
} catch (Throwable $e) {
	ExceptionHandler::handle($e);
}