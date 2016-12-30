$(document).bind('pageinit', 'div:jqmData(role="page"), div:jqmData(role="dialog")', function() {
    
    function updateChordSize() {
        var val = $('input[name=ctrl_chord_size]:checked').val();
        switch (val) {
            case 'hidden':
                $(".chord").addClass("hidden");
                $(".chord").removeClass("chord_small");
                $(".chord").removeClass("chord_medium");
                $(".chord").removeClass("chord_large");
                break;
            case 's':
                $(".chord").removeClass("hidden");
                $(".chord").addClass("chord_small");
                $(".chord").removeClass("chord_medium");
                $(".chord").removeClass("chord_large");
                break;
            default:
            case 'm':
                $(".chord").removeClass("hidden");
                $(".chord").removeClass("chord_small");
                $(".chord").addClass("chord_medium");
                $(".chord").removeClass("chord_large");
                break;
            case 'l':
                $(".chord").removeClass("hidden");
                $(".chord").removeClass("chord_small");
                $(".chord").removeClass("chord_medium");
                $(".chord").addClass("chord_large");
                break;
        }
    }

    // Update on change of the radio group:
    $('input[name=ctrl_chord_size]').change(function() {
        updateChordSize();
    });

    // Set initial settings:
    updateChordSize();

});

/*$(document).ready(function() {
    
    var updateChordSize = function() {
        var val = $('input[name=ctrl_chord_size]').val();
        var em = val/10;
        $('.chord').css('font-size', em + 'em');
        $('.chord').css('line-height', em + 'em');
    };
    
    $('input[name=ctrl_chord_size]').bind("change", function(e, ui) {
        updateChordSize();
    });
    
    // Set initial value:
    updateChordSize();
    
});*/