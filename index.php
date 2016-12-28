<?php

    /**
     *  CONFIGURATION
     */

    /* DEBUGGING */

    error_reporting(E_ALL);


    /**
     *  INCLUDES
     */
    
    include_once ("modules/parser.php");
    

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
        
        $parser = new Parser();
        
        $parser->readFile($file);
        $sheet = $parser->parseSheet();

    }



?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="3rdparty/snap.js/snap.css" />
        <link rel="stylesheet" type="text/css" href="3rdparty/fontawesome/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />

        <script type="text/javascript" src="3rdparty/jquery/jquery-1.12.3.min.js"></script>
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
                <?php
                    include ("widgets/list-sheets.php");
                ?>
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
                <div id="sheet-header">

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
                    
                </div>
                <div id="sheet-body">

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
                        if (isset($block["type"])) {
                            $css_class = $block["type"];
                        }

                        // Add css class for modifier if applicable:
                        if (isset($block["modifier"])) {
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

        <script type="text/javascript" src="3rdparty/snap.js/snap.js"></script>
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
