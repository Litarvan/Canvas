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
use Canvas\Canvas;

/**
 * The Search Controller
 *
 * Manage the search system
 *
 * @package Canvas
 * @version 1.0.0-BETA
 * @author  TheShark34
 */
class SearchController
{

    public function display()
    {
        return Paladin::view("search.twig");
    }

    public function search()
    {
        if (!isset($_POST["search"]) || !isset($_POST["search-type"]))
            return Paladin::view("error.twig", array("title" => "Missing argument !", "message" => "Some arguments are missing !"));

        $searchType = $_POST["search-type"];
        $search = $_POST["search"];
        $files = self::listFiles("files");
        $groupResults = array();
        $artifactResults = array();

        if ($searchType == "groupsOnly")
            $groupsOnly = true;
        else
            $groupsOnly = false;

        if ($searchType == "artifactsOnly")
            $artifactsOnly = true;
        else
            $artifactsOnly = false;

        if (!$artifactsOnly)
            foreach ($files["groups"] as $g)
                if (strpos($g, $search) !== false || strpos(str_replace(".", " ", $g), $search) !== false)
                    $groupResults[sizeof($groupResults)] = $g;

        if (!$groupsOnly)
            foreach ($files["artifacts"] as $a)
                if (strpos($a, $search) !== false)
                    $artifactResults[sizeof($artifactResults)] = $a;

        $groupTree = Canvas::makeTree(null, $groupResults, null, "%f", true);
        $artifactTree = Canvas::makeTree(null, $artifactResults, "%f", null, true);

        return Paladin::view("search-result.twig", array("groups" => $groupTree, "artifacts" => $artifactTree));
    }

    public function listFiles($folder, $finalFiles = array("groups" => array(), "artifacts" => array()))
    {
        $files = Canvas::listWithoutUnwanted($folder);

        foreach ($files as $file)
            if (!is_dir($folder . "/" . $file))
                continue;
            else if (Canvas::is_artifact($folder . "/" . $file))
                $finalFiles["artifacts"][sizeof($finalFiles["artifacts"])] = $folder . "/" . $file;
            else
            {
                $finalFiles["groups"][sizeof($finalFiles["groups"])] = $folder . "/" . $file;
                $finalFiles = array_merge($finalFiles, self::listFiles($folder . "/" . $file, $finalFiles));
            }

        return $finalFiles;
    }
}
