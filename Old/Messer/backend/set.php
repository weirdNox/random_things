<?
$currentSettings = array(
    array(
        "name" => "Rolar no chão",
        "codename" => "rolar",
        "probability" => 0,
        "resources" => array(
            "rolar"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Harlem Shake",
        "codename" => "harlem",
        "probability" => 0,
        "resources" => array(
            "harlem"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Katamari Hack",
        "codename" => "katamari",
        "probability" => 0,
        "resources" => array(
            "jquery.min",
            "katamari"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Flashing background",
        "codename" => "flash",
        "probability" => 0,
        "resources" => array(
            "flash"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Gravity effect",
        "codename" => "gravity",
        "probability" => 0,
        "resources" => array(
            "jquery.min",
            "gravity"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Ameno",
        "codename" => "ameno",
        "probability" => 0,
        "resources" => array(
            "ameno"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Message",
        "codename" => "message",
        "probability" => 0,
        "resources" => array(
            "message"
        ),
        "settings" => array(
            "title" => "",
            "content" => "",
            "warningLevel" => ""
        )
    ),

    array(
        "name" => "Nyan Cat",
        "codename" => "nyan",
        "probability" => 0,
        "resources" => array(
            "nyan"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "Je suis Charlie",
        "codename" => "charlie",
        "probability" => 0,
        "resources" => array(
            "charlie"
        ),
        "settings" => array(
        )
    ),

    array(
        "name" => "None",
        "codename" => "none",
        "probability" => 0,
        "resources" => array(
        ),
        "settings" => array(
        )
    )
);

$settingsInfo = array(
    "message" => array(
        "title" => array(
            "name" => "Title",
            "type" => "string"
        ),
        "content" => array(
            "name" => "Content",
            "type" => "string"
        ),
        "warningLevel" => array(
            "name" => "Warning level",
            "type" => "option",
            "availableOptions" => array(
                "Low" => "low",
                "Medium" => "medium",
                "High" => "high",
            )
        )
    )
);

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["messerSettings"])) {
    foreach($currentSettings as &$effect)
    {
        $prob = testInput($effect["codename"] . "-prob");
        $effect["probability"] = is_numeric($prob) && $prob >= 0 ? $prob : 0;

        foreach($effect["settings"] as $key => $value)
        {
            $effect["settings"][$key] = testInput($effect["codename"] . "-" . $key);
        }
    }

    $file = fopen("messer.json", "w");
    fwrite($file, json_encode($currentSettings));
    fclose($file);
}
elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["liveTrigger"])) {
    $toFile = array(
        "effect" => json_decode($_POST["effect"], true),
        "action" => testInput("action")
    );

    $file = fopen("live.json", "w+");
    if(flock($file, LOCK_EX))
    {
        ftruncate($file, 0);
        fwrite($file, json_encode($toFile));
        fflush($file);
        flock($file, LOCK_UN);
    }
    fclose($file);

    exit();
}
elseif(file_exists("messer.json"))
{
    $fileSettings = json_decode(file_get_contents("messer.json"), true);
    if(array_intersect_key($currentSettings, $fileSettings) == $currentSettings)
    {
        $currentSettings = $fileSettings;
    }
}

function testInput($name) {
    if(!isset($_POST[$name])) {
        return NULL;
    }

    $data = trim($_POST[$name]);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Mess with Messer's settings!</title>
        <meta name="description" content="Change Messer settings from anywhere!">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="normalize.css">
        <style type="text/css">
        	body {
        		padding: 25px;
        	}

            input[type=checkbox], input[type=number] {
                margin-left: 10px;
            }

            fieldset {
                margin: 5px;
            }

            li h4 {
                margin-bottom: 5px;
            }

            li {
                list-style: none;
            }

            input[type=submit] {
                margin: 5px;
            }
        </style>
    </head>
    <body>
        <h1>Messer settings</h1>

        <button type="button" id="trigger-toggle" onclick="buttons=document.querySelectorAll('button.trigger-button');if(buttons[0].disabled){for(var i=0; i<buttons.length; i++) {buttons[i].disabled = false;}this.innerText = 'Disable live triggering';}else{for(var i=0; i<buttons.length; i++) {buttons[i].disabled = true;} this.innerText = 'Enable live triggering';}">Enable live triggering</button>

        <button type="button" class="stop-button" onclick="stop();" style="padding: 5px 15px;">Stop!</button>

        <form action="" method="post">
            <fieldset>
                <h3>Effects settings</h3>
                <ul>
                    <?
                    foreach($currentSettings as $effect)
                    {
                        echo "<li id='".$effect["codename"]."' data-resources='".json_encode($effect["resources"])."'>";
                        printf("<h4>%s</h4>", $effect["name"]);
                        printf("<label>Probability:</label><input type=\"number\" name=\"%s\" value=\"%d\">", $effect["codename"] . "-prob", $effect["probability"]);

                        foreach($effect["settings"] as $key => $value)
                        {
                            if($settingsInfo[$effect["codename"]][$key]["type"] == "string")
                            {
                                printf("<label>%s:</label><input type=\"text\" name=\"%s\" value=\"%s\">",
                                $settingsInfo[$effect["codename"]][$key]["name"], $effect["codename"] . "-" . $key, $value);
                            }

                            elseif($settingsInfo[$effect["codename"]][$key]["type"] == "option")
                            {
                                printf("<label>%s:</label><select name=\"%s\">",
                                $settingsInfo[$effect["codename"]][$key]["name"], $effect["codename"] . "-" . $key);

                                foreach($settingsInfo[$effect["codename"]][$key]["availableOptions"] as $optionName => $optionCode)
                                {
                                    $selected = "";
                                    if($value === $optionCode)
                                    {
                                        $selected = "selected=\"selected\"";
                                    }
                                    printf("<option value=\"%s\" %s>%s</option>", $optionCode, $selected, $optionName);
                                }
                                printf("</select>");
                            }
                        }

                        echo "<br /><button type=\"button\" class=\"trigger-button\" onclick=\"execute(this.parentElement)\" disabled>Execute!</button>";

                        echo "</li>";
                    }
                    ?>
                <button type="button" onclick="inputs = document.querySelectorAll('input[type=number]');for(var i=0; i<inputs.length; i++) {inputs[i].value = 0;}" style="padding: 5px 15px;">Reset everything!</button>
            </fieldset>

            <input type="submit" name="messerSettings" value="Submit settings">
        </form>

        <script type="text/javascript">
            function execute(parentElem)
            {
                var effect = {"codename":"none", "name":"None", "resources":[], "settings":{}};

                effect.codename = parentElem.getAttribute('id');
                effect.resources = JSON.parse(parentElem.dataset.resources);

                var iterator = parentElem.childNodes;
                for(i=0; i < iterator.length; i++)
                {
                    var elem = parentElem.childNodes[i];
                    if(elem.tagName == "H4")
                    {
                        effect.name = elem.innerText;
                    }
                    else if(elem.getAttribute("name") && elem.getAttribute("name").indexOf(effect.codename) > -1)
                    {
                        var setting = elem.getAttribute("name").replace(effect.codename+"-", "");
                        if(setting != "prob")
                        {
                            effect.settings[setting] = elem.value;
                        }
                    }
                }

                var request = new XMLHttpRequest();
                request.open("POST", window.location.pathname, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                var params = "liveTrigger=1&action=execute&effect=" + encodeURIComponent(JSON.stringify(effect));
                request.send(params);
            }
            function stop()
            {
                var request = new XMLHttpRequest();
                request.open("POST", window.location.pathname, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                var params = "liveTrigger=1&action=stop&effect=" + encodeURIComponent(JSON.stringify({"codename":"none", "name":"None", "resources":[], "settings":{}}));
                request.send(params);
            }
        </script>
    </body>
</html>
