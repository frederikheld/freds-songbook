<?php

    include_once(dirname (__FILE__) . "/../modules/parser.php");

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Read all files from directory "sheets":
if ($handle = opendir (dirname (__FILE__) . "/../sheets/")) {

    $sheets = array();
    while (false !== ($entry = readdir($handle))) {
        if (!in_array($entry, array(".", "..", ".gitignore", "README.md"))) {
            array_push($sheets, $entry);
        }
    }
    // TODO: How did the README.md get into that array? It isn't even inside that directory!
    
//    echo "<pre>Debug \$sheets:";
//    print_r($sheets);
//    echo "</pre>";
    
    closedir ($handle);
} else {
    echo "<p>ERROR: Could not read directory 'sheets'";
}


// Print list of sheets:
?>
<ul class="songs">
<?php

/**
 *  Extract artist and title:
 */
function get_song_details($entry) {
    // TODO: Put parser into class, then use it here

    // Parse sheet:
    $parser = new Parser();
    $parser->readFile(dirname (__FILE__) . "/../sheets/" . $entry);
    $sheet = $parser->parseSheet();

    // Extract and return result:
    $result = array (
        "filename"  => $entry,
        "artist"    => $sheet["meta"]["artist"],
        "title"     => $sheet["meta"]["title"]
    );
    return $result;
}

foreach ($sheets as $entry) {
    
    $result = get_song_details($entry);
    // TODO: This call is extremely costly because it fully parses every sheet in the book!
    //       I need caching or some kind of file based dbms to avoid that heavy operation
    //       for each page loaded!
    echo "<li><a href=\"" . $_SERVER["SCRIPT_NAME"] . "?sheet=" . $result["filename"] . "\">" . $result["artist"] . " - " . $result["title"] . "</a></li>";
        
}
    
    
?>
</ul>
<?php

?>
<!--
<ul class="songs">
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=hotel_california.txt">The Eagles: Hotel California</a></li>
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=my_immortal.txt">Evanescence: My Immortal</a></li>
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=boeser_wolf.txt">Die Toten Hosen: Böser Wolf</a></li>
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=over_the_rainbow.txt">Judy Garland: Over the rainbow</a></li>
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=hallelujah.txt">Rufus Wainwright: Hallelujah</a></li>
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=summerwine.txt">Ville Valo & Natalia Avalon: Summerwine</a></li>
    <li><a href="<?php echo $_SERVER["SCRIPT_NAME"]; ?>?sheet=bosse-schoenste_zeit.txt">Bosse: Schönste Zeit</a></li>
</ul>//-->
