<?php

include_once("modules/widget.php");

class WidgetDisplayPage extends Widget {
    
    public function __construct($context) {
        parent::__construct($context);
        
        // Run this widget only if a page is requested:
        if (!isset($this->context["page"])) {
            return false;
        }
        
        // Default stylesheet gets autoloaded
        
        // Init views:
        $this->addView(new ViewPage($this->context), "page");
        
        // Load page:
        $this->common["parsed_page"] = $this->loadPage($this->context["page"]);
    }
    
    private function loadPage($filename) {
        
        $file = "contents/pages/" . $filename;
        
        // Check, if selected file exists:
        if (!file_exists($file)) {
            $file = "contents/pages/error.txt";
        }
        
        // Load file:
        $file_contents = file_get_contents($file);
        
        return $file_contents;
        
    }
    
}

class ViewPage extends View {
    
    public function render($common) {
        echo "<div class=\"static\">";     // INFO: .static here, because #page is used by jQuery Mobile and .page could be confusing!
        echo $common["parsed_page"];
        echo "</div>";
    }
    
}
