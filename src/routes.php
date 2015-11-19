<?php

/*
 * Load all your routes here ! Exemple :
 *
 * Paladin::getRouteManager()->get('myroute/{myargument}', 'MyController@getMyroute');
 * post('myotheroute', ['middleware' => 'mymiddleware', 'use' => function() { return display('myview.blade.php', 'blade') }]);
 * 
 * See the doc for more !
 */

use Paladin\Paladin;

$this->get("/", function() {
    return \Paladin\Paladin::view("home.twig");
});

$this->get("/tree", "TreeController@group");
$this->get("/tree/:groupId", "TreeController@group");
$this->get("/tree/:groupId/:artifactId", "TreeController@artifact");
$this->get("/package/:groupId/:artifactId/:version", "PackageController@show");

?>