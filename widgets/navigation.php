<?php

include_once("modules/widget.php");
include_once("modules/parser.php");
// TODO: Path assumes that this script is included in index.php. Implement global config that sets a basepath to avoid problems with includes! Change includes in all widgets!

class WidgetNavigation extends Widget {
    
//    protected $shared_data;

    public function __construct($g) {
        parent::__construct($g);
        
//        $this->shared_data["sheets"] = array();
        
        // Add views:
        $this->addView(new ViewSheetsNav(), "nav-sheets");
        $this->addView(new ViewPagesNav(), "nav-pages");
        
        // Get filenames of all sheets:
        $this->common["sheets"]["filenames"] = $this->getSheetsFilenames();
        
        // Get song meta from each sheet:
        $this->common["sheets"]["meta"] = $this->getSheetsMeta();
    }
    
    private function getSheetsFilenames() {
        
        // Read all files from directory "sheets":
        if ($handle = opendir ("contents/sheets")) {

            $sheets = array();
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array(".", "..", ".gitignore", "README.md"))) {
                    array_push($sheets, $entry);
                }
            }
            // TODO: How did the README.md get into that array? It isn't even inside that directory!
            //       Answer: It is there, because it exists in the directory. But why does NetBeans not show it?
            
            closedir ($handle);
            return $sheets;
            
        } else {
            
            echo "<p>ERROR: Could not read directory 'sheets'";
            return null;
            
        }

    }
    
    private function getSheetsMeta() {

        $result = array();
        foreach ($this->common["sheets"]["filenames"] as $filename) {

            $meta = $this->getSongMeta($filename);
            // TODO: This call is extremely costly because it fully parses every sheet in the book!
            //       I need caching or some kind of file based dbms to avoid that heavy operation
            //       for each page loaded!
            
            array_push($result, $meta);

        }
        
        return $result;
        
    }
    
    /**
     *  Extract artist and title from given filename:
     */
    private function getSongMeta($filename) {

        // Parse sheet:
        $parser = new Parser();
        $parser->readFile("contents/sheets/" . $filename);
        $sheet = $parser->parseSheet();

        // Extract and return result:
        $result = array (
            "filename"  => $filename,
            "artist"    => (isset($sheet["meta"]["artist"]) ? $sheet["meta"]["artist"] : ""),
            "title"     => (isset($sheet["meta"]["title"]) ? $sheet["meta"]["title"] : "")
        );
        return $result;
        
    }
    
}

class ViewSheetsNav extends View {
    
    public function render($data) {
        
        asort ($data["sheets"]["meta"]);
        
        $result = "<ul data-role=\"listview\" data-filter=\"true\" data-filter-placeholder=\"Search sheets...\">\n";
        foreach ($data["sheets"]["meta"] as $meta) {
            $result .= "\t<li><a href=\"" . $_SERVER["SCRIPT_NAME"] . "?sheet=" . $meta["filename"] . "\">" . $meta["artist"] . " - " . $meta["title"] . "</a></li>\n";
        }
        $result .= "</ul>\n";
        
        return $result;
    }
    
}

class ViewPagesNav extends View {
    
    public function render($data) {
        $result  = "<ul data-role=\"listview\">\n";
        $result .= "\t<li><a href=\"?page=welcome.txt\">Start</a></li>\n";
        $result .= "\t<li><a href=\"?page=make_a_sheet.txt\">Make a Sheet</a></li>\n";
        $result .= "\t<li><a href=\"?page=not_existing.txt\">This page does not exist!</a></li>\n";
        $result .= "</ul>\n";
        
        return $result;
    }
}