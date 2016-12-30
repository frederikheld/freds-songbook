<?php

include_once("modules/widget.php");

class WidgetSheetControls extends Widget {
    
    public function __construct($context) {
        parent::__construct($context);
        
        // Run this widget only if a sheet is requested:
        if (!isset($this->context["sheet"])) {
            return false;
        }
        
        // Default stylesheet gets autoloaded
        
        // Init views:
        $this->addView(new ViewControls($this->context), "sheet-controls");
        
    }
    
}

class ViewControls extends View {
    
    public function render($common) {
        
?>
<div class="sheet_controls">
    <h1>Controls</h1>
    <fieldset data-role="controlgroup" data-type="horizontal" id="ctrl_chord_size">
        <legend>Chord size:</legend>
        <input type="radio" name="ctrl_chord_size" id="ctrl_chord_size_hidden" value="hidden" />
        <label for="ctrl_chord_size_hidden"><span class="fa fa-eye-slash"></span></label>
        <input type="radio" name="ctrl_chord_size" id="ctrl_chord_size_s" value="s" />
        <label for="ctrl_chord_size_s">S</label>
        <input type="radio" name="ctrl_chord_size" id="ctrl_chord_size_m" value="m" checked="checked" />
        <label for="ctrl_chord_size_m">M</label>
        <input type="radio" name="ctrl_chord_size" id="ctrl_chord_size_l" value="l" />
        <label for="ctrl_chord_size_l">L</label>
    </fieldset>
    
<?php /*
 * 
 * This is another approach that could be used in the future to allow more steps
 * (20 steps between 0% and 200% of original size)
 * The corresponding js is found in sheet-controls.js but a little buggy with the
 * initial size.
    <form class="full-width-slider">
        <label for="ctrl_chord_size">Chord Size:</label>
        <input name="ctrl_chord_size" id="ctrl_chord_size" min="0" max="20" value="10" type="range" />
    </form>
*/ ?>
</div>
        
<?php
    }
    
}
