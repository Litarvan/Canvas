<?php

/*
 * Copyright 2015 TheShark34
 *
 * This file is part of Paladin.
 *
 * Paladin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Paladin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Paladin.  If not, see <http://www.gnu.org/licenses/>.
 */

use Paladin\Paladin;
use Paladin\ErrorHandling\PaladinErrorHandler;

// The bootstrap, load all the little things then start Paladin !
// If you wan't to add some classes to load, or things like that,
// you can do it there !

// Do your things here !

// Starting Paladin !
try {
	Paladin::start(array(
	    "configFolder"      => "config",
	    "sourceFolder"      => "src",
	    "controllerFolder"  => "Controllers",
	    "middlewareFolder"  => "Middlewares",
	    "modelFolder"       => "Models",
	    "resourceFolder"    => "resources",
	    "viewFolder"        => "views",
	    "mainEngine"		=> "twig",
	), function()
	{
		PaladinErrorHandler::setErrorPageLocation("views/ErrorPage.html");

		if(Paladin::getConfigLoader()->getConfigs()["app"]["debug"])
		{
			$engine = Paladin::getViewingEngineManager()->getSelectedEngine();

			if(get_class($engine) == "Paladin\Viewing\TwigViewingEngine")
			{
				$engine->getTwig()->enableAutoReload();
				$engine->getTwig()->enableDebug();
			}
		}
	});
} catch (\Exception $e) {
	if($e instanceof \Paladin\Routing\RouteNotFoundException)
	{
		// If the file is in the repo, downloading it ;)
		$file = "files/" . trim(str_replace(dirname($_SERVER['SCRIPT_NAME']), "", $_SERVER['REQUEST_URI']), "/");

		if(file_exists($file))
		{
			$response = new \Paladin\Http\RedirectResponse(Paladin::getRootPath(true) . $file);
			$response->send();

			return;
		}
	}

	PaladinErrorHandler::displayErrorPage("Exception caught ! " . get_class($e), $e->getMessage(), function_exists("debug_backtrace") ? PaladinErrorHandler::generateBacktrace() : "");
}

// Dead code after this point

?>