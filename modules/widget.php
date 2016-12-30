<?php

/**
 * Be aware: The widget runs the payload, the views display the result.
 * If you have heavy operations, whose results are used by only one view
 * think of moving that view to a different widget!
 */
abstract class Widget {
    
    
    /*
     *  MEMBERS
     */
    
    protected $context;     // Stores the data, that comes from the outside and provides context information
    protected $views;       // Stores the views of this widget
    protected $common;      // Stores the data that the views in this widget share

    protected static $stylesheets = array();    // Stores links to stylesheets that are added by the instances of this class
    protected static $scripts = array();    // Stores links to scripts that are added by the instances of this class
    
    /*
     *  MAGIC METHODS
     */
    
    /*
     * The constructor tries to autoload the default css and js files for this widget.
     * 
     * Naming scheme:
     *      class name:     WidgetAnimatedUnicorns
     *      php filename:   animated-unicorns.php
     *      css filename:   animated-unicorns.css
     *      js filename:    animated-unicorns.js
     * 
     * Both default files have to be placed in "widgets/"
     * 
     * Additional css files can be loaded with $this->addStylesheet($link)
     * Additional js files can be loaded with $this->addScript($link)
     * 
     * Create and add views in the constructor of your child class!
     */
    public function __construct($context) {
        
        // Init members:
        $this->context = $context;
        $this->views = array();
        $this->common = null; // Can be anything. In the beginning it is just nothing.
        
        // Load default stylesheet and script:
        $default_filename_stub = "widgets/" . substr(ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', get_class($this))), '-'), 7); // Source: http://stackoverflow.com/a/19533226 with own additions
        
        $stylesheet = $default_filename_stub . ".css";
        if (file_exists($stylesheet)) {
            $this->addStylesheet($stylesheet);
//            echo "Stylesheet '" . $stylesheet . "' was autoloaded.\n";
        } else {
//            echo "Could not autoload stylesheet '" . $stylesheet . "'\n";
        }
        
        $script = $default_filename_stub . ".js";
        if (file_exists($script)) {
            $this->addScript($script);
//            echo "Script '" . $script . "' was autoloaded.\n";
        } else {
//            echo "Could not autoload script '" . $script . "'\n";
        }
        
    }
    
    public function __destruct() { }
    
    /**
     * Add a view to this widget.
     * If a view with the same name already exists it will
     * be overwritten!
     */
    public function addView($view, $name) {
        $this->views[$name] = $view;
    }
    
    /**
     * Returns the rendered HTML code for the view with
     * the given name.
     * 
     * Usage:
     *      echo $mywidget->renderView("viewname");
     */
    public function renderView($name) {
        if (isset($this->views[$name])) {
            $view = $this->views[$name];
            return $view->render($this->common);
        } else {
            return "ERROR: View '" . $name . "' does not exist in widget '" . get_class($this) . "'";
        }
    }
    
    /**
     * Use this function to inject the colected stylesheets from all widgets
     * into the page.
     */
    public static function injectStylesheets() {
        // TODO: Add option to compress all styles into one single file
        //       or even add them directly into the html file.
        // FIXME: This mechanism is great because it injects only the css, that
        //        is currently needed.
        //        Unfortunately jQuery Mobile does not update the page correctly,
        //        when switching between pages and sheets :-(
        foreach (self::$stylesheets as $stylesheet) {
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $stylesheet . "\" />\n";
        }
    }
    
    /**
     * Use this function to inject the colected scripts from all widgets
     * into the page.
     */
    public static function injectScripts() {
        // TODO: Same as with injectCSS().
        foreach (self::$scripts as $script) {
            echo "<script type=\"text/javascript\" src=\"" . $script . "\"></script>";
        }
    }
    
    /**
     * Add a stylesheet to the widget. It will be automatically injected into the
     * page at the position where injectStylesheets() is called.
     * 
     * Usage:
     * 
     *      class MyWidget extends Widget { ... }
     *      $mywidget = new MyWidget();
     *      $mywidget->addStylesheet("path/to/stylesheet.css");
     * 
     * @param type $stylesheet
     */
    protected function addStylesheet($stylesheet) {
        array_push(self::$stylesheets, $stylesheet);
    }
    
    /**
     * Add a script to the widget. It will be automatically injected into the
     * page at the position where injectScripts() is called.
     * 
     * For usage and further information see addStylesheet().
     * 
     * @param type $script
     */
    protected function addScript($script) {
        array_push(self::$scripts, $script);
    }
    
}

abstract class View {
    
    
    /*
     *  MEMBERS
     */


    /*
     *  MAGIC METHODS
     */
    
    public function __construct() {
        
    }
    
    public function __destruct() {
        
    }
    
    /**
     * Renders the view with the given set of data.
     * Implement the payload of the view in this function!
     * 
     * @param type $common
     */
    public abstract function render($common);
}