<?php

    // error_reporting(E_ALL); // for development use
    error_reporting(0); // for productive use
    
    // TODO: Disarm all user input at once
    
    
    /*
     *  INIT WIDGETS
     */
    
    // TODO: Widgets are run, even if none of their views are used in this page
    //       (display-sheet vs. display-page).
    //       Init widgets only if needed (context-sensitive)!
    //       Right now all widgets have to be initialized, otherwise their CSS
    //       will not be injected into the page. jQuery Mobile needs all CSS
    //       on the first page load since it won't upate the whole html when
    //       switching between sheets and static pages.
    
    // IDEA: Inject css inside the #page div together with the widget? Is this allowed?
    
    // TODO: To have two different widgets for mutually exclusive contents
    //       (sheets and pages) blows up development because I always have to
    //       distinguish between the both.
    
    // Compile context data:
    $context["sheet"] = null;
    $context["page"] = null;
    if (isset($_GET["sheet"])) {
        $context["sheet"] = filter_input(INPUT_GET, "sheet");
    } elseif (isset($_GET["page"])) {
        $context["page"] = filter_input(INPUT_GET, "page");
    } else {
        $context["page"] = "welcome.txt";
    }
    
    // Init navigation:
    include_once ("widgets/navigation.php");
    $widget_navigation = new WidgetNavigation($context);
    
    if (isset($context["sheet"])) {
        
        // Init sheet display:
        include_once ("widgets/display-sheet.php");
        $widget_display_sheet = new WidgetDisplaySheet($context);
    
        // Init sheet controls:
        include_once ("widgets/sheet-controls.php");
        $widget_sheet_controls = new WidgetSheetControls($context);
    }
    
    if (isset($context["page"])) {
        // Init page display:
        include_once ("widgets/display-page.php");
        $widget_display_page = new WidgetDisplayPage($context);
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Fred's Songbook</title>
        
        <!-- Load normalize.css -->
        <link rel="stylesheet" type="text/css" href="3rdparty/normalize.css/normalize.css" />
        
        <!-- Load jQuery Mobile -->
        <link rel="stylesheet" type="text/css" href="3rdparty/jquery.mobile/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css" />
        <script type="text/javascript" src="3rdparty/jquery/jquery-1.12.3.min.js"></script>
        <script type="text/javascript" src="3rdparty/jquery.mobile/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
        
        <!-- Load Fontawesome -->
        <link rel="stylesheet" type="text/css" href="3rdparty/fontawesome/font-awesome.min.css" />
        
        <!-- Load global app styles -->
        <link rel="stylesheet" type="text/css" href="styles.css" />
        
        <!-- Make jQuery Mobile panels swipeable -->
        <script type="text/javascript">
        $(document).on('pageinit', 'div:jqmData(role="page")', function() {
            $(document).on('swipeleft swiperight', '#mainpage', function(e) {
                // We check if there is no open panel on the page because otherwise
                // a swipe to close the left panel would also open the right panel (and v.v.).
                // We do this by checking the data that the framework stores on the page element (panel: open).
                if ($.mobile.activePage.jqmData('panel') !== 'open') {
                    if (e.type === 'swipeleft') {
                        $('#panel-right').panel('open');
                    } else if (e.type === 'swiperight') {
                        $('#panel-left').panel('open');
                    }
                }
            });
        });
        // Source:  http://demos.jquerymobile.com/1.3.2/examples/panels/panel-swipe-open.html
        </script>
        
    </head>
    <body>
        
        <div id="mainpage" data-role="page">
            
            <?php
                /*
                 * I don't know if it is allowed to add stylesheets in the html
                 * body (for scripts it seems to be allowed) but it works and it
                 * is the only way I found to get styles added by the widgets
                 * loaded on demand.
                 */
            ?>
            
            <!-- Inject stylesheets from widgets -->
            <?php Widget::injectStylesheets(); ?>

            <!-- Inject scripts from the widgets -->
            <?php Widget::injectScripts(); ?>
            
            <div class="header" data-role="header" data-position="fixed">
                <span class="open left"><a href="#panel-left" class="ui-btn ui-corner-all ui-btn-icon-notext ui-alt-icon ui-nodisc-icon ui-btn-left ui-icon-bars"></a></span>
                <h1>Fred's Songbook</h1>
                <span class="open right"><a href="#panel-right" class="ui-btn ui-corner-all ui-btn-icon-notext ui-alt-icon ui-nodisc-icon ui-btn-right ui-icon-gear"></a></span>
            </div>
            
            <div class="content" data-role="content">
                <?php
                    if (isset($_GET["sheet"])) {
                        echo $widget_display_sheet->renderView("sheet");
//                        echo "DEBUG: Displaying a sheet (says index.php)";
                    } else {
                        echo $widget_display_page->renderView("page");
//                        echo "DEBUG: Displaying a page (says index.php)";
                    }
                ?>
            </div>
            
            <div class="footer" data-role="footer" data-position="fixed">
                <h1><a href="https://github.com/frederikheld/freds-songbook">Contribute on GitHub!</a></h1>
            </div>
            
            <div class="panel left" data-role="panel" data-position="left" data-position-fixed="true" data-display="reveal" id="panel-left">
                <h1>Pages</h1>
                <?php echo $widget_navigation->renderView("nav-pages"); ?>
                <h1>Sheets</h1>
                <?php echo $widget_navigation->renderView("nav-sheets"); ?>
            </div>
            
            <div class="panel right" data-role="panel" data-position="right" data-position-fixed="true" data-display="reveal" id="panel-right">
                
                <?php
                    if (isset($_GET["sheet"])) {
                        echo $widget_sheet_controls->renderView("sheet-controls");
                        echo $widget_display_sheet->renderView("sheet-meta");
                    }
                ?>
            </div>
            
        </div>
        
    </body>
</html>
