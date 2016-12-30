<?php

include_once("modules/widget.php");
include_once("modules/parser.php");

/**
 * TODO: I really don't like how this widget can also display static pages,
 *       although there is a distinct widget for that.
 *       The whole architecture mixes up sheets and static pages, so everything
 *       has to be redesigned and rewritten. For now, it solves the purpose.
 */

class WidgetDisplaySheet extends Widget {

    public function __construct($context) {
        parent::__construct($context);
        
        // Run this widget only if a sheet is requested:
        if (!isset($this->context["sheet"])) {
            return false;
        }
        
        // Add stylesheet:
//        $this->addStylesheet("display-sheet.css"); // Not neccessary since the default stylesheet was already autoloaded by parent::__construct()
        
        // Add views:
        $this->addView(new ViewSheet($this->common), "sheet");
        $this->addView(new ViewSheetMeta($this->common), "sheet-meta");
        
        // Gather data:
        $file = "contents/sheets/" . $this->context["sheet"];
        if (file_exists($file)) {
            
            // Load and parse sheet:
            $this->common["parsed_sheet"] = $this->parseSheet($this->context["sheet"]);
            
        } else {
            
            // Change context:
            unset($this->context["sheet"]);
            $this->context["page"] = "error.txt";
            
            // Load page:
            $this->common["loaded_page"] = $this->loadPage($this->context["page"]);
        }
    }
    
    private function loadPage($filename) {
        $file = "contents/pages/" . $filename;
        return file_get_contents($file);
    }
    
    private function parseSheet($filename) {
        
        $file = "contents/sheets/" . $filename;
        
        // Parse sheet:
        $parser = new Parser();
        $parser->readFile($file);
        $parsed_sheet = $parser->parseSheet();

        // DEBUG:
//        ob_start();
//        echo "<pre>";
//        print_r($parsed_sheet);
//        echo "</pre>";
//        $output = ob_get_contents();
//        ob_end_clean();
//        echo $output;

        return $parsed_sheet;
        
    }
    
}

class ViewSheet extends View {
    
    public function render($shared_data) {
        
        if (isset($shared_data["parsed_sheet"])) {
            return $this->renderSheet($shared_data["parsed_sheet"]);
        }
        
        if (isset($shared_data["loaded_page"])) {
            return $this->renderPage($shared_data["loaded_page"]);
        }
        
        return "Something went terribly wrong in ViewSheet of WidgetDisplaySheet :'-(";
        
    }
    
    private function renderPage($page) {        
        return $page;
    }
    
    private function renderSheet($sheet) {

        ob_start();
        // The upcoming output block is a mess. I just copied and pasted it from
        // the first draft. Hence the output buffer to save time for the rewrite.
        // Which still has to be done...
        // TODO: Rewrite upcoming block!
?>

<div class="sheet">
    <div class="sheet-header">

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
    <div class="sheet-body">

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
                /**
                 *  PRINT LINES IN BLOCK
                 */
                foreach ($block["lines"] as $line):
            ?>
            <p><?php echo $line; ?></p>
            <?php endforeach; ?>

        </div>

        <?php endforeach; ?>

    </div>

</div>

<?php
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }
    
}

class ViewSheetMeta extends View {
    
    public function render($shared_data) {
        
        $sheet = $shared_data["parsed_sheet"];
        
        ob_start();
        
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

<?php
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }
    
}