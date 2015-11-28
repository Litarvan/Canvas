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

namespace Canvas;

use Paladin\Std\StringsUtil;

/**
 * The Canvas main class
 *
 * Contains a lot of useful methods
 *
 * @package Canvas
 * @version 1.0.0-BETA
 * @author  TheShark34
 */
class Canvas
{
    public static function makeTree($folder, $files, $artifactDesc, $groupDesc, $pathInFiles = false)
    {
        $tree = array();

        foreach ($files as $file)
        {
            if (!is_dir((!$pathInFiles ? ($folder . "/") : "") . $file) || StringsUtil::startsWith($file, "."))
                continue;

            $json = (!$pathInFiles ? ($folder . "/") : "") . $file . "/infos.json";
            $isArtifact = self::is_artifact((!$pathInFiles ? ($folder . "/") : "") . $file);

            $formattedFile = str_replace("/", ".", $file);
            if (StringsUtil::startsWith($formattedFile, "files."))
                $formattedFile = substr($formattedFile, 6);


            if ($isArtifact)
                $finalFile = array("file" => $formattedFile, "name" => $formattedFile, "desc" => str_replace("%f", $formattedFile, $artifactDesc), "type" => "artifact");
            else
                $finalFile = array("file" => $formattedFile, "name" => $formattedFile, "desc" => str_replace("%f", $formattedFile, $groupDesc), "type" => "group");

            if (file_exists($json))
                $finalFile = array_merge($finalFile, self::getInfos($json, $formattedFile, $isArtifact ? str_replace("%f", $formattedFile, $artifactDesc) : str_replace("%f", $formattedFile, $groupDesc)));

            $finalFile["icon"] = self::getIfExists((!$pathInFiles ? ($folder . "/") : "") . $file . "/icon.png");

            $tree[sizeof($tree)] = $finalFile;
        }

        return $tree;
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

    public static function getInfos($json, $defaultName, $defaultDesc)
    {
        $json = json_decode(file_get_contents($json));
        $json = (array)$json;

        $infos["name"] = isset($json["name"]) ? $json["name"] : $defaultName;
        $infos["desc"] = isset($json["desc"]) ? $json["desc"] : $defaultDesc;

        return $infos;
    }

    public static function getIfExists($path)
    {
        return file_exists($path) ? $path : "";
    }

    public static function getGroupName($groupId)
    {
        $explodedGroup = explode(".", $groupId);

        return $explodedGroup[sizeof($explodedGroup) - 1];
    }

    public static function listWithoutUnwanted($folder)
    {
        $files = scandir($folder);
        $files = array_slice($files, 2);

        return $files;
    }
}