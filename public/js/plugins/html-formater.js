$.fn.minify = function() {
    return val = $(this).val(), val.replace(/\t/gi, "").replace(/\n/gi, "").replace(/\s\s+/gi, "")
}, $.prettify_code = function(e) {
    ! function e(a) {
        if (a.parent().length > 0 && a.parent().data("assign") ? a.data("assign", a.parent().data("assign") + 1) : a.data("assign", 1), a.children().length > 0) a.children().each(function() {
            for (tbc = "", i = 0; i < $(this).parent().data("assign"); i++) tbc += "\t";
            $(this).before("\n" + tbc), $(this).prepend("\t"), $(this).append("\n" + tbc), e($(this))
        });
        else {
            for (tbc = "", i = 0; i < a.parent().data("assign"); i++) tbc += "\t";
            a.prepend("\n" + tbc)
        }
    }(e)
}, $.fn.show_code = function(e) {
    $(this).val(e)
}, $.replace_tag = function(e) {
    return e = (e = (e = (e = (e = (e = e.replace(/<html/i, '<div id="replace_html"')).replace(/<\/html>/i, "</div>*-html-*")).replace(/<head/i, '<div id="replace_head"')).replace(/<\/head>/i, "</div>*-head-*")).replace(/<body/i, '<div id="replace_body"')).replace(/<\/body>/i, "</div>*-body-*")
}, $.undo_tag = function(e) {
    return e = (e = (e = (e = (e = (e = e.replace('<div id="replace_html"', "<html")).replace("</div>*-html-*", "</html>")).replace('<div id="replace_head"', "<head")).replace("</div>*-head-*", "</head>")).replace('<div id="replace_body"', "<body")).replace("</div>*-body-*", "</body>")
};