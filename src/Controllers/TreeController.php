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
use Paladin\Std\StringsUtil;

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

        $files = scandir($folder);
        $files = array_slice($files, 2);

        $finalFiles["artifacts"] = array();
        $finalFiles["groups"] = array();

        foreach ($files as $file)
            if (!is_dir($folder . "/" . $file))
                continue;
            else if (self::is_artifact($folder . "/" . $file))
            {
                $json = $folder . "/" . $file . "/infos.json";
                $file = array("file" => $file, "name" => $file, "desc" => "Artifact : " . $groupId . " >> " . $file);

                if (file_exists($json))
                {
                    $json = json_decode(file_get_contents($json));
                    $json = (array) $json;

                    $file["name"] = isset($json["name"]) ? $json["name"] : $file["name"];
                    $file["desc"] = isset($json["desc"]) ? $json["desc"] : $file["desc"];
                    $file["icon"] = file_exists($folder . "/" . $file["file"] . "/icon.png") ? $folder . "/" . $file["file"] . "/icon.png" : "";
                }
                $finalFiles["artifacts"][sizeof($finalFiles["artifacts"])] = $file;
            }
            else
            {
                $json = $folder . "/" . $file . "/infos.json";
                $file = array("file" => $file, "name" => $file, "desc" => "Group : " . $groupId . ($groupId != "" ? "." : "") . $file);

                if (file_exists($json))
                {
                    $json = json_decode(file_get_contents($json));
                    $json = (array) $json;

                    $file["name"] = isset($json["name"]) ? $json["name"] : $file["name"];
                    $file["desc"] = isset($json["desc"]) ? $json["desc"] : $file["desc"];
                    $file["icon"] = file_exists($folder . "/" . $file["file"] . "/icon.png") ? $folder . "/" . $file["file"] . "/icon.png" : "";
                }
                $finalFiles["groups"][sizeof($finalFiles["groups"])] = $file;
            }

        $explodedGroup = explode(".", $groupId);
        $group = array("name" => $explodedGroup[sizeof($explodedGroup) - 1], "desc" => $groupId, "id" => $groupId);

        $json = $folder . "/infos.json";
        if(file_exists($json))
        {
            $json = json_decode(file_get_contents($json));
            $json = (array) $json;

            $group["name"] = isset($json["name"]) ? $json["name"] : $file["name"];
            $group["desc"] = isset($json["desc"]) ? $json["desc"] : $file["desc"];
            $group["icon"] = file_exists($folder . "/icon.png") ? $folder . "/icon.png" : "";
        }

        return Paladin::view("tree.twig", array("groups" => $finalFiles["groups"], "artifacts" => $finalFiles["artifacts"], "path" => $path, "group" => $group, "type" => sizeof($finalFiles["artifacts"]) > 0 ? "artifact" : "group"));
    }

    public static function is_artifact($file)
    {
        $files = scandir($file);
        foreach ($files as $folder)
            if (is_dir($file . "/" . $folder))
            {
                $artifactFiles = scandir($file . "/" . $folder);
                foreach ($artifactFiles as $f)
                    if (StringsUtil::endsWith($file . "/" . $folder . "/" . $f, ".jar"))
                        return true;
            }

        return false;
    }

    public function artifact($groupId, $artifactId)
    {
        $folder = "files/" . str_replace(".", "/", $groupId) . "/" . $artifactId;
        $path = explode(".", $groupId);
        $path[sizeof($path)] = "/" . $artifactId;

        if (!file_exists($folder) || !is_dir($folder))
            return Paladin::view("error.twig", array("title" => "Can't find this group !", "message" => "You requested an unknown group id (${groupId})", "path" => $path));

        $files = scandir($folder);
        $files = array_slice($files, 2);

        $versions = array();

        foreach ($files as $file)
        {
            $json = $folder . "/" . $file . "/infos.json";
            $file = array("file" => $file, "name" => $file, "desc" => "Artifact : " . $groupId . " >> " . $artifactId . " >>> " . $file);

            if (file_exists($json))
            {
                $json = json_decode(file_get_contents($json));
                $json = (array) $json;

                $file["name"] = isset($json["name"]) ? $json["name"] : $file["name"];
                $file["desc"] = isset($json["desc"]) ? $json["desc"] : $file["desc"];
                $file["icon"] = file_exists($folder . "/" . $file["file"] . "icon.png") ? $folder . "/" . $file["file"] . "icon.png" : "";
            }
            $versions[sizeof($versions)] = $file;
        }

        return Paladin::view("tree.twig", array("versions" => $versions, "path" => $path, "group" => $groupId, "artifact" => $artifactId, "type" => "version"));
    }

}
