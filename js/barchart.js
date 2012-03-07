jQuery(document).ready(function($) {
    var r = new Raphael(window.rc_chart_settings.id),
        fin = function () {
            this.flag = r.popup(this.bar.x, this.bar.y, this.bar.value || "0").insertBefore(this);
        },
        fout = function () {
            this.flag.animate({opacity: 0}, 300, function () {this.remove();});
        },
        fin2 = function () {
            var y = [], res = [];
            for (var i = this.bars.length; i--;) {
                y.push(this.bars[i].y);
                res.push(this.bars[i].value || "0");
            }
            this.flag = r.popup(this.bars[0].x, Math.min.apply(Math, y), res.join(", ")).insertBefore(this);
        },
        fout2 = function () {
            this.flag.animate({opacity: 0}, 300, function () {this.remove();});
        },
        txtattr = { font: "12px sans-serif" };
    var height = jQuery("#"+window.rc_chart_settings.id).height();
    var width = jQuery("#"+window.rc_chart_settings.id).width();
    bchart = r.barchart(
        1,
        1,
        height,
        width,
        window.rc_chart_settings.vals,
        {
            labelcolor: window.rc_chart_settings.legendcolor,
            type: window.rc_chart_settings.endtype,
            colors: window.rc_chart_settings.colors
        }
    );
        if (window.rc_chart_settings.hover == 'on') { bchart.hover(fin, fout); }
        if (window.rc_chart_settings.labels.length > 0) { bchart.label(window.rc_chart_settings.labels, true); }
});