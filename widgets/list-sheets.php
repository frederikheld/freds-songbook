<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Read all files from directory "sheets":
if ($handle = opendir ("../sheets")) {

    $sheets = array();
    while (false !== ($entry = readdir($handle))) {
        if (!in_array($entry, array(".", ".."))) {
            array_push($sheets, $entry);
        }
    }

    // Extract artist and title:
    $sheets_detailled = array_map ("get_song_details", $sheets);
    function get_song_details($entry) {
        // TODO: Put parser into class, then use it here
    }
    
    echo "<pre>";
    print_r($sheets);
    echo "</pre>";
    
    closedir ($handle);
} else {
    echo "<p>ERROR: Could not read directory 'sheets'";
}


// Print list of sheets:
?>
<ul class="songs">
<?php

foreach ($sheets as $entry) {
    echo "<li><a href=\"" . $_SERVER["SCRIPT_NAME"] . "?sheet=" . $entry . "\">" . $entry . "</a></li>";
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
