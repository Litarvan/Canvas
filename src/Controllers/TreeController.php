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
 * The Tree Controller
 *
 * Manage the tree
 *
 * @package Canvas
 * @version 1.0.0-BETA
 * @author  TheShark34
 */
class TreeController
{
    public function group($groupId = "")
    {
        $folder = "files/" . str_replace(".", "/", $groupId);
        $path = explode(".", $groupId);

        if (!file_exists($folder) || !is_dir($folder))
            return Paladin::view("error.twig", array("title" => "Can't find this group !", "message" => "You requested an unknown group id (${groupId})", "path" => $path));

        $files = Canvas::listWithoutUnwanted($folder);
        $tree = Canvas::makeTree($folder, $files, "Artifact : " . $groupId . " >> %f", "Group : " . $groupId . ($groupId != "" ? "." : "") . "%f");
        $groupName = Canvas::getGroupName($groupId);

        $group = array("name" => $groupName, "desc" => $groupId, "id" => $groupId, "icon" => Canvas::getIfExists($folder . "/icon.png"));
        $json = $folder . "/infos.json";

        if (file_exists($json))
            $group = array_merge($group, Canvas::getInfos($json, $groupName, $groupId));

        return Paladin::view("tree.twig", array("files" => $tree, "path" => $path, "group" => $group, "type" => "tree"));
    }

    public function artifact($groupId, $artifactId)
    {
        $folder = "files/" . str_replace(".", "/", $groupId) . "/" . $artifactId;
        $path = explode(".", $groupId);
        $path[sizeof($path)] = "/" . $artifactId;

        if (!file_exists($folder) || !is_dir($folder))
            return Paladin::view("error.twig", array("title" => "Can't find this group !", "message" => "You requested an unknown group id (${groupId})", "path" => $path));

        $files = Canvas::listWithoutUnwanted($folder);
        $versions = array();
        $json = $folder . "/infos.json";
        $infos = array("name" => $artifactId, "desc" => $groupId, "icon" => Canvas::getIfExists($folder . "/icon.png"));

        if (file_exists($json))
            $infos = array_merge($infos, Canvas::getInfos($json, $infos["name"], $infos["desc"]));

        foreach ($files as $file)
        {
            if (!is_dir($folder . "/" . $file))
                continue;

            $file = array("version" => $file, "desc" => "Artifact : " . $groupId . " >> " . $artifactId . " >>> " . $file);
            $versions[sizeof($versions)] = $file;
        }

        return Paladin::view("tree.twig", array("versions" => $versions, "path" => $path, "group" => $groupId, "artifact" => $artifactId, "type" => "version", "infos" => $infos));
    }

}
