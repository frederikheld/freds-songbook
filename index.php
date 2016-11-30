<?php

    /**
     *  CONFIGURATION
     */


    /* DEBUGGING */

    error_reporting(E_ALL);


    /**
     *  CONSTANTS
     */


    /* ALLOWED VALUES */

    $block_types = array("intro", "verse", "chorus", "bridge", "outro");
    $block_modifiers = array("chords", "tabs");


    /* REGEXES */

    $regex_chord = "~\[(.+?)\]~";                       // [chord]
    $regex_meta = "~{{(.+?):(.+?)}}~";                  // {{key:value}}
    $regex_blockstart = "~{{(.+?)(\|(.+?))*?:}}~";      // {{blockname:}} or {{blockname|modifyer:}}
    $regex_blockmarker = "~{{(.+?)}}~";                 // {{blockmarker}}



    /**
     *  LOAD SHEET
     */

    // Decide which file to load and sanitize user input:
    if (isset($_GET["sheet"])) {

        $file = "sheets/" . strip_tags(trim($_GET["sheet"]));
        $file_type = "sheet";

    } else {

        if (isset($_GET["page"])) {
            $file = "pages/" . strip_tags(trim($_GET["page"]));
            $file_type = "page";
        } else {
            $file = "pages/welcome.txt";
            $file_type = "page";
        }
    }

    // Check if selected file exists:
    if (!file_exists($file)) {
        $file = "pages/error.txt";
    }



    // Activate interpreter only for sheets:
    if ($file_type == "sheet") {

        // Init:
        $sheet = array();
        $sheet["order"] = array();

        // Read lines in array:
        $handle = fopen($file, "rb");
        $lines = array();
        while ($line = fgets($handle)) {
            array_push ($lines, $line);
        }


    /**
     *  EXTRACT INFORMATION FROM SHEET
     */

        // Loop over all lines:
        $n = 0;
        while ($n < count($lines)) {


            // Search for meta:
            $meta = array();
            if (1 == preg_match($regex_meta, $lines[$n], $meta)) {
                $sheet["meta"][$meta[1]] = $meta[2];

                // Continue with next cycle:
                $n++;
                continue;
            }


            // Search for blockstart:
            $blockname = "";
            if (1 == preg_match($regex_blockstart, $lines[$n], $blockname)) {
                foreach ($block_types as $type) {
                    if (false !== strpos($blockname[1], $type)) {

                        // Save block type:
                        $sheet["blocks"][$blockname[1]]["type"] = $type;

                        // Check for modifier and save it in case:
                        if (isset($blockname[3])) {
                            $sheet["blocks"][$blockname[1]]["modifier"] = $blockname[3];
                        }

                        break;
                    }
                }

                // Save block name in block order array:
                array_push($sheet["order"], $blockname[1]);
                $sheet["blocks"][$blockname[1]]["lines"] = array();

                // Add all lines until an empty line was found:
                $n++;
                while (0 != strlen(trim($lines[$n]))) {
                    array_push($sheet["blocks"][$blockname[1]]["lines"], $lines[$n]);

                    // Continue with next cycle:
                    $n++;
                    if ($n >= count($lines)) break 2; // Stop parsing if index is beyond scope.
                    continue;
                }
            }


            // Search for block placeholder:
            $blockmarker = "";
            if (1 == preg_match($regex_blockmarker, $lines[$n], $blockmarker)) {

                array_push($sheet["order"], $blockmarker[1]);

                // Continue with next cycle:
                $n++;
                continue;
            }


            $n++;

        }



        /**
         *  REPLACE PLACEHOLDERS IN SHEET
         */

        $count = 0;
        $helper = function($line) {
            global $count;
            global $regex_chord;

            return preg_replace($regex_chord, "<span class=\"chord\">$1</span>", $line, -1, $count);
        };

        $newblocks = array();
        foreach ($sheet["blocks"] as $key => $block) {
            $newblocks[$key] = array_map($helper, $block);
            if ($count > 0) {
                $newblocks[$key]["modifier"] = "chords";
            }
        }

        $sheet["blocks"] = $newblocks;

    }



?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="libs/snap.js/snap.css" />
        <link rel="stylesheet" type="text/css" href="libs/fontawesome/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />

        <script type="text/javascript" src="libs/jquery/jquery-1.12.3.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                $("#btn_chords_hide").click(function(event) {
                    event.preventDefault();

                    $(".chord").addClass("hidden");
                    $(".chord").removeClass("chord_small");
                    $(".chord").removeClass("chord_medium");
                    $(".chord").removeClass("chord_large");

                    $("#btn_chords_hide").addClass("btn_active");
                    $("#btn_chords_small").removeClass("btn_active");
                    $("#btn_chords_medium").removeClass("btn_active");
                    $("#btn_chords_large").removeClass("btn_active");
                });

                $("#btn_chords_small").click(function(event) {
                    event.preventDefault();

                    $(".chord").removeClass("hidden");
                    $(".chord").addClass("chord_small");
                    $(".chord").removeClass("chord_medium");
                    $(".chord").removeClass("chord_large");

                    $("#btn_chords_hide").removeClass("btn_active");
                    $("#btn_chords_small").addClass("btn_active");
                    $("#btn_chords_medium").removeClass("btn_active");
                    $("#btn_chords_large").removeClass("btn_active");
                });

                $("#btn_chords_medium").click(function(event) {
                    event.preventDefault();

                    $(".chord").removeClass("hidden");
                    $(".chord").removeClass("chord_small");
                    $(".chord").addClass("chord_medium");
                    $(".chord").removeClass("chord_large");

                    $("#btn_chords_hide").removeClass("btn_active");
                    $("#btn_chords_small").removeClass("btn_active");
                    $("#btn_chords_medium").addClass("btn_active");
                    $("#btn_chords_large").removeClass("btn_active");
                });

                $("#btn_chords_large").click(function(event) {
                    event.preventDefault();

                    $(".chord").removeClass("hidden");
                    $(".chord").removeClass("chord_small");
                    $(".chord").removeClass("chord_medium");
                    $(".chord").addClass("chord_large");

                    $("#btn_chords_hidden").removeClass("btn_active");
                    $("#btn_chords_small").removeClass("btn_active");
                    $("#btn_chords_medium").removeClass("btn_active");
                    $("#btn_chords_large").addClass("btn_active");
                });

                // Set initial settings:
                $("#btn_chords_medium").click();

            });
        </script>
    </head>
    <body id="body">

        <div class="snap-drawers">

            <div id="index" class="snap-drawer snap-drawer-left">
                <h1>Songs</h1>
                <ul>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>">Start</a></li>
                </ul>
                <ul class="songs">
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=hotel_california.txt">The Eagles: Hotel California</a></li>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=my_immortal.txt">Evanescence: My Immortal</a></li>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=boeser_wolf.txt">Die Toten Hosen: Böser Wolf</a></li>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=over_the_rainbow.txt">Judy Garland: Over the rainbow</a></li>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=hallelujah.txt">Rufus Wainwright: Hallelujah</a></li>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=summerwine.txt">Ville Valo & Natalia Avalon: Summerwine</a></li>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=bosse-schoenste_zeit.txt">Bosse: Schönste Zeit</a></li>
                </ul>
                <ul>
                    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?page=make_a_sheet.txt">Make a sheet!</a></li>
                </ul>
            </div>

            <div id="sheetmeta" class="snap-drawer snap-drawer-right">

                <?php if ($file_type == "sheet"): ?>

                    <div class="controls">
                        <h1>Settings</h1>
                        <h2>chord size</h2>
                        <div>
                            <button title="hide chords" id="btn_chords_hide" class="button"><span class="fa fa-eye-slash"></span></button>
                            <button title="small chords" id="btn_chords_small" class="button"><span class="">S</span></button>
                            <button title="medium chords" id="btn_chords_medium" class="button"><span class="">M</span></button>
                            <button title="large chords" id="btn_chords_large" class="button"><span class="">L</span></button>
                        </div>
                    </div>

                    <?php
                        if (
                            isset($sheet["meta"]["source"]) ||
                            isset($sheet["meta"]["year"])
                        ):
                    ?>
                    <div class="meta">
                        <h1>Meta</h1>
                        <table>
                        <?php
                            echo (isset($sheet["meta"]["source"]) ? "<tr><td>Source:</td><td>" . $sheet["meta"]["source"] . "</td></tr>" : "");
                            echo (isset($sheet["meta"]["year"]) ? "<tr><td>Year:</td><td>" . $sheet["meta"]["year"] . "</td></tr>" : "");
                        ?>
                        </table>
                    </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        </div>

        <div id="content" class="snap-content">

            <?php if ($file_type == "sheet"): ?>

            <div id="sheet">

                    <?php /* DEBUG
                    <pre><?php print_r($sheet); ?></pre>
                    <hr />
                    <pre><?php //echo $file; ?></pre>
                    <pre><?php //print_r($sheet["meta"]); ?></pre>
                    */ ?>

                    <?php
                        /**
                         *  PRINT SHEET HEADER
                         */
                    ?>
                    <h1><?php echo (isset($sheet["meta"]["title"]) ? $sheet["meta"]["title"] : "<unknown title>"); ?></h1>
                    <h2><?php
                        echo (isset($sheet["meta"]["artist"]) ? $sheet["meta"]["artist"] : "<unkown artist>");
                        echo (isset($sheet["meta"]["original_artist"]) ? "<br />(" . $sheet["meta"]["original_artist"] . ")" : "");
                        echo (isset($sheet["meta"]["from"]) ? "<br />(from: " . $sheet["meta"]["from"] . ")" : "");
                    ?></h2>

                    <?php
                        /**
                         *  PRINT INSTRUCTIONS
                         */

                        if (
                            isset($sheet["meta"]["capo"])
                        ):
                    ?>
                    <div class="instructions">
                        <?php
                            if (isset($sheet["meta"]["capo"])) {
                                echo "<p>Capo " . $sheet["meta"]["capo"] . "</p>";
                            }
                        ?>
                    </div>
                    <?php endif; ?>

                    <?php
                        /**
                         *  PRINT BLOCKS
                         */
                        foreach ($sheet["order"] as $id):
                    ?>

                    <?php

                        // Select current block:
                        $block = $sheet["blocks"][$id];

                        // Determine css class for block type:
                        $css_class = "";
                        if (isset($block["type"]) && in_array($block["type"], $block_types)) {
                            $css_class = $block["type"];
                        }

                        // Add css class for modifier if applicable:
                        if (isset($block["modifier"]) && in_array($block["modifier"], $block_modifiers)) {
                            $css_class .= " " . $block["modifier"];
                        }

                        // Print block:
                    ?>
                    <div class="<?php echo $css_class; ?>">

                        <?php
                            foreach ($block["lines"] as $line):
                            /**
                             *  PRINT LINES IN BLOCK
                             */
                        ?>
                        <p><?php echo $line; ?></p>
                        <?php endforeach; ?>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <?php if ($file_type == "page"): ?>

                <div id="page">
                    <?php include ($file); ?>
                </div>

            <?php endif; ?>

        </div>

        <div id="header" class="">
            <button title="Open Index" id="open-left" class="button"><span class="fa fa-list-ul"></span></button>
            <button title="Open Sheet Settings" id="open-right" class="button"><span class="fa fa-cog"></span></button>
        </div>

        <script type="text/javascript" src="libs/snap.js/snap.js"></script>
        <script type="text/javascript">

            /* Init Snap */
            var snapper = new Snap ({
                element: document.getElementById('content'),
                dragger: null
            });

            /* Buttons in header to open drawers */
            $("#open-left").on("click", function() {
                if (snapper.state().state == "left") {
                    snapper.close();
                } else {
                    snapper.open("left");
                    $('#open-left').addClass("btn_active");
                    // Actually done by the "animated" event, but added here
                    // to avoid ugly flickering on hover.
                }
            });

            $("#open-right").on("click", function() {
                if (snapper.state().state == "right") {
                    snapper.close();
                } else {
                    snapper.open("right");
                    $('#open-right').addClass("btn_active");
                    // Actually done by the "animated" event, but added here
                    // to avoid ugly flickering on hover.
                }
            });

            snapper.on("animated", function() {
                if (snapper.state().state == "left") {
                    $('#open-left').addClass("btn_active");
                }
                if (snapper.state().state !== "left") {
                    $('#open-left').removeClass("btn_active");
                }

                if (snapper.state().state == "right") {
                    $('#open-right').addClass("btn_active");
                }
                if (snapper.state().state !== "right") {
                    $('#open-right').removeClass("btn_active");
                }
            });

            /* Fix header */
            // snapper.on("start", function() {
                // if (snapper.state().state !== "left" && snapper.state().state !== "right") {
                    // $("#content").append(document.getElementById("header"));
                    // $("#content").css("top", "0");
                    // $("#content").css("overflow-y", "hidden");
                    // $("#sheet").css("margin-top", "3.4em");
                // }
            // });
            // snapper.on("animated", function() {
                // if (snapper.state().state !== "left" && snapper.state().state !== "right") {
                    // $("#body").append(document.getElementById("header"));
                    // $("#content").css("top", "3.4em");
                    // $("#content").css("overflow-y", "scroll");
                    // $("#sheet").css("margin-top", "0");
                // }
            // });

            /* all snapper events for debugging */
            /*snapper.on("animated", function() {
                console.log("snapper animated");
            });

            snapper.on("drag", function() {
                console.log("snapper drag");
            });

            snapper.on("end", function() {
                console.log("snapper end");
            });

            snapper.on("start", function() {
                console.log("snapper start");
            });

            snapper.on("close", function() {
                console.log("snapper close");
            });

            snapper.on("open", function() {
                console.log("snapper open");
            });

            snapper.on("expandLeft", function() {
                console.log("snapper expandLeft");
            });

            snapper.on("expandRight", function() {
                console.log("snapper expandRight");
            });*/

            /* Prevent Safari opening links when viewing as a Mobile App */
            (function (a, b, c) {
                if(c in b && b[c]) {
                    var d, e = a.location,
                        f = /^(a|html)$/i;
                    a.addEventListener("click", function (a) {
                        d = a.target;
                        while(!f.test(d.nodeName)) d = d.parentNode;
                        "href" in d && (d.href.indexOf("http") || ~d.href.indexOf(e.host)) && (a.preventDefault(), e.href = d.href)
                    }, !1)
                }
            })(document, window.navigator, "standalone");
        </script>

    </body>
</html>
