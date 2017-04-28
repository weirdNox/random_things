<?
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/json; charset=utf-8");


function parseFile()
{
    $contents = file_get_contents("live.json");

    if($contents === FALSE)
    {
        return [];
    }

    $parsed = json_decode($contents, true);

    return $parsed;
}

// Get current settings
if(isset($_REQUEST["settings"]))
{
    echo file_get_contents("messer.json");
}

// Return last update time
elseif(isset($_REQUEST["getLastUpdateTime"]))
{
    clearstatcache();
    echo filemtime("live.json");
}

// Wait for live.json change to trigger
elseif(isset($_REQUEST["waitForTrigger"]))
{
    clearstatcache();
    $firstTime = 0;
    if(isset($_REQUEST["lastUpdateTime"]) && is_numeric($_REQUEST["lastUpdateTime"]))
    {
        $firstTime = $_REQUEST["lastUpdateTime"];
    }
    else
    {
        echo "Must have last update time!";
        exit();
    }

    for($i = 0; $i < 30; $i++)
    {
        clearstatcache();
        $parsed = parseFile();

        if(empty($parsed))
        {
            sleep(1);
            continue;
        }

        if(filemtime("live.json") > $firstTime)
        {
            echo json_encode(["lastUpdateTime" => filemtime("live.json"), "gotLive" => 1, "effect" => $parsed["effect"], "action" => $parsed["action"]]);
            exit();
        }

        sleep(1);
    }

    echo json_encode(["lastUpdateTime" => $firstTime, "gotLive" => 0, "effect" => ["codename" => "none", "name" => "None", "resources" => [], "settings" => []], "action" => "stop"]);
}
else
{
    echo "Nothing to see here.";
}
