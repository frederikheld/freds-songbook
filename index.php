<?php

    /**
     *  CONFIGURATION
     */

    /* DEBUGGING */

    error_reporting(E_ALL);



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
                
                <?php
                    if (isset($_GET["sheet"])) {
                        $file_type = "sheet";
                    }
                    // TODO: $file_type is used in widgets/display-sheet.php as well. It should be set at the beginning of index.php and used by both!
                ?>

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
                            echo (isset($sheet["meta"]["year"]) ? "<tr><td>Year:</td><td>" . $sheet["meta"]["year"] . "</td></tr>" : "");
                            echo (isset($sheet["meta"]["album"]) ? "<tr><td>Album:</td><td>" . $sheet["meta"]["album"] . "</td></tr>" : "");
                            echo (isset($sheet["meta"]["source"]) ? "<tr><td>Source:</td><td>" . $sheet["meta"]["source"] . "</td></tr>" : "");
                            echo (isset($sheet["meta"]["listen"]) ? "<tr><td>Listen:</td><td><a href=\"" . $sheet["meta"]["listen"] . "\">" . $sheet["meta"]["listen"] . "</a></td></tr>" : "");
                            // TODO: Parser should add the surrounding tags. Maybe I need something like "engines" for different ourposes like a "meta engine", "chords engine", ...
                        ?>
                        </table>
                    </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        </div>

        <div id="content" class="snap-content">
            <?php include("widgets/display-sheet.php"); ?>
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
