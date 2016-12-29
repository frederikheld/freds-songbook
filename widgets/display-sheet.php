<?php

// TODO: Tie CSS file to widget

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


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