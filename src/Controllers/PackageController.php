<?php

/*
 * Copyright 2015 Adrien Navratil
 *
 * This file is part of Canvas.
 *
 * Canvas is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Canvas is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Canvas.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Canvas\Controllers;

use Paladin\Paladin;

class PackageController
{
    public function show($groupId, $artifactId, $version)
    {
        $pomFile = "files/" . str_replace(".", "/", $groupId) . "/$artifactId/$version/$artifactId-$version.pom";
        $path = explode(".", $groupId);
        $path[sizeof($path)] = "/" . $artifactId;

        if(!file_exists($pomFile))
            return Paladin::view("error.twig", array("title" => "Malformed package !", "message" => "Can't find the pom file for this package !(Searched at $pomFile)", "path" => $path));

        $pom = simplexml_load_file($pomFile);

        $name = isset($pom->name) ? $pom->name : $artifactId;
        $url = isset($pom->url) ? $pom->url : false;

        return Paladin::view("package.twig", array("name" => $name, "url" => $url, "group" => $groupId, "artifact" => $artifactId, "version" => $version, "path" => $path));
    }
}
