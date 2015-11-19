<?php

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

    public function group($groupId)
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
            if (self::is_artifact($folder . "/" . $file))
                $finalFiles["artifacts"][sizeof($finalFiles["artifacts"])] = $file;
            else
                $finalFiles["groups"][sizeof($finalFiles["groups"])] = $file;

        return Paladin::view("tree.twig", array("groups" => $finalFiles["groups"], "artifacts" => $finalFiles["artifacts"], "path" => $path, "group" => $groupId, "type" => sizeof($finalFiles["artifacts"]) > 0 ? "artifact" : "group"));
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

        return Paladin::view("tree.twig", array("versions" => $files, "path" => $path, "group" => $groupId, "artifact" => $artifactId, "type" => "version"));
    }

}
