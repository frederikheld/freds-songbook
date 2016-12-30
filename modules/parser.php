<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Parser {


    /**
     *  CONSTANTS
     */


    /* allowed blocknames */
    
    protected $BLOCKS = array (
        "types"         => array ("intro", "verse", "chorus", "bridge", "outro"),
        "modifiers"     => array ("chords", "tabs")
    );

    /* regexes */
    
    protected $REGEXES = array (
        "chord"         => "~\[(.+?)\]~",               // [chord]
        "meta"          => "~{{(.+?):(.+?)}}~",         // {{key:value}}
        "blockstart"    => "~{{(.+?)(\|(.+?))*?:}}~",   // {{blockname:}} or {{blockname|modifyer:}}
        "blockmarker"   => "~{{(.+?)}}~"                // {{blockmarker}}
    );
    
    
    /**
     *  MEMBERS
     */
    
    protected $lines_raw = array();   // Contains the lines to parse
    
    
    /**
     *  MAGIC FUNCTIONS
     */
    
    
    public function __construct() {
        
    }
    
    public function __destruct() {
        
    }
    
    
    /**
     *  METHODS
     */
    
    /**
     * Reads file contents line by line into an array
     * and stores the result in the internal variable
     *      $this->lines_raw
     * 
     * @param type $path
     */
    public function readFile($path) {
        
        // DEBUG:
//        echo "\n\nContext in parser.php:";
//        echo "\n\$path:";
//        print_r ($path);

        // Read lines in array:
        $lines_raw = array();
        if (file_exists($path)) {
            $handle = fopen($path, "rb");
            while ($line = fgets($handle)) {
                array_push ($lines_raw, $line);
            }
        
            // Store result in member:
            $this->lines_raw = $lines_raw;
            
        } else {
            echo "ERROR: File \"" . $path . "\" not found in parser.php";
            return false;
        }
        
        return true;
        
    }
    
    /**
     * Parses a array of strings.
     */
    public function parseSheet() {  

        // Init:
        $sheet = array();
        $sheet["order"] = array();


    /**
     *  EXTRACT INFORMATION FROM SHEET
     */

        // Loop over all lines:
        $n = 0;
        $lines_raw = $this->lines_raw;
        while ($n < count($lines_raw)) {

            // Search for meta:
            $meta = array();
            if (1 == preg_match($this->REGEXES["meta"], $lines_raw[$n], $meta)) {
                $sheet["meta"][$meta[1]] = $meta[2];

                // Continue with next line:
                $n++;
                continue;
            }

            // Search for blockstart:
            $blockname = "";
            if (1 == preg_match($this->REGEXES["blockstart"], $lines_raw[$n], $blockname)) {
                foreach ($this->BLOCKS["types"] as $type) {
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
                while (0 != strlen(trim($lines_raw[$n]))) {
                // TODO: The parser throws a "undefined offset" warning in the line above if there are only empty lines after the last opening tag.
                    array_push($sheet["blocks"][$blockname[1]]["lines"], $lines_raw[$n]);

                    // Continue with next cycle:
                    $n++;
                    if ($n >= count($lines_raw)) break 2; // Stop parsing if index is beyond scope.
                    continue;
                }
            }


            // Search for block placeholder:
            $blockmarker = "";
            if (1 == preg_match($this->REGEXES["blockmarker"], $lines_raw[$n], $blockmarker)) {

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

            return preg_replace($this->REGEXES["chord"], "<span class=\"chord\">$1</span>", $line, -1, $count);
        };
        
        // DEBUG:
//        echo "\n\nContext in parser.php:";
//        echo "\n\$sheet:";
//        print_r ($sheet);

        $newblocks = array();
        foreach ($sheet["blocks"] as $key => $block) { // TODO: This line crashes if the sheet contains no blocks (undefined index)
            
            $newblocks[$key] = array_map($helper, $block);
            if ($count > 0) {
                $newblocks[$key]["modifier"] = "chords";
            }
        }

        $sheet["blocks"] = $newblocks;
        
        
        // Return result:
        return $sheet;
    }
    
    
}