(function (e, t) {
    "use strict";
    if (!e) {
        return t;
    }
    var n = function () {
        this.el = t;
        this.items = t;
        this.sizes = [];
        this.max = [0, 0];
        this.current = 0;
        this.interval = t;
        this.opts = {speed: 500, delay: 3e3, complete: t, keys: !t, dots: t, fluid: t};
        var n = this;
        this.init = function (t, n) {
            this.el = t;
            this.ul = t.children("ul");
            this.max = [t.outerWidth(), t.outerHeight()];
            this.items = this.ul.children("li").each(this.calculate);
            this.opts = e.extend(this.opts, n);
            this.setup();
            return this
        };
        this.calculate = function (t) {
            var r = e(this), i = r.outerWidth(), s = r.outerHeight();
            n.sizes[t] = [i, s];
            if (i > n.max[0])n.max[0] = i;
            if (s > n.max[1])n.max[1] = s
        };
        this.setup = function () {
            this.el.css({overflow: "hidden", width: n.max[0], height: this.items.first().outerHeight()});
            this.ul.css({width: this.items.length * 100 + "%", position: "relative"});
            this.items.css("width", 100 / this.items.length + "%");
            if (this.opts.delay !== t) {
                this.start();
                this.el.hover(this.stop, this.start)
            }
            this.opts.keys && e(document).keydown(this.keys);
            this.opts.dots && this.dots();
            if (this.opts.fluid) {
                var r = function () {
                    n.el.css("width", Math.min(Math.round(n.el.outerWidth() / n.el.parent().outerWidth() * 100), 100) + "%")
                };
                r();
                e(window).resize(r)
            }
            if (this.opts.arrows) {
                this.el.parent().append('<p class="arrows"><span class="prev">â†</span><span class="next">â†’</span></p>').find(".arrows span").click(function () {
                    e.isFunction(n[this.className]) && n[this.className]()
                })
            }
            if (e.event.swipe) {
                this.el.on("swipeleft", n.prev).on("swiperight", n.next)
            }
        };
        this.move = function (t, r) {
            if (!this.items.eq(t).length)t = 0;
            if (t < 0)t = this.items.length - 1;
            var i = this.items.eq(t);
            var s = {height: i.outerHeight()};
            var o = r ? 5 : this.opts.speed;
            if (!this.ul.is(":animated")) {
                n.el.find(".dot:eq(" + t + ")").addClass("active").siblings().removeClass("active");
                this.el.animate(s, o) && this.ul.animate(e.extend({left: "-" + t + "00%"}, s), o, function (i) {
                    n.current = t;
                    e.isFunction(n.opts.complete) && !r && n.opts.complete(n.el)
                })
            }
        };
        this.start = function () {
            n.interval = setInterval(function () {
                n.move(n.current + 1)
            }, n.opts.delay)
        };
        this.stop = function () {
            n.interval = clearInterval(n.interval);
            return n
        };
        this.keys = function (t) {
            var r = t.which;
            var i = {37: n.prev, 39: n.next, 27: n.stop};
            if (e.isFunction(i[r])) {
                i[r]()
            }
        };
        this.next = function () {
            return n.stop().move(n.current + 1)
        };
        this.prev = function () {
            return n.stop().move(n.current - 1)
        };
        this.dots = function () {
            var t = '<ol class="dots">';
            e.each(this.items, function (e) {
                t += '<li class="dot' + (e < 1 ? " active" : "") + '">' + (e + 1) + "</li>"
            });
            t += "</ol>";
            this.el.addClass("has-dots").append(t).find(".dot").click(function () {
                n.move(e(this).index())
            })
        }
    };
    e.fn.unslider = function (t) {
        var r = this.length;
        return this.each(function (i) {
            var s = e(this);
            var u = (new n).init(s, t);
            s.data("unslider" + (r > 1 ? "-" + (i + 1) : ""), u)
        })
    }
})(window.jQuery, false);
(function ($) {
    window.addRule = function (selector, styles, sheet) {
        if (typeof styles !== "string") {
            var clone = "";
            for (var style in styles) {
                var val = styles[style];
                style = style.replace(/([A-Z])/g, "-$1").toLowerCase(); // convert to dash-case
                clone += style + ":" + (style === "content" ? '"' + val + '"' : val) + "; ";
            }
            styles = clone;
        }
        sheet = sheet || document.styleSheets[0];
        sheet.addRule(selector, styles);
        return this;
    };
    if ($) {
        $.fn.addRule = function (styles, sheet) {
            addRule(this.selector, styles, sheet);
            return this;
        };
    }
}(window.jQuery));


(function ($, window, document, undefined) {

    "use strict";

    var pluginName = "xSlider",
        defaults = {
            bullets: true,
            timeout: 5000,
            brightness: 0.6
        };

    function Plugin(element, options) {
        this.element = element;

        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            this.configure();

            this.stylize();

            this.attatch();
        },

        configure: function () {
            if ($(this.element).data('x-slider-timeout')) {
                this.settings.timeout = $(this.element).data('x-slider-timeout');
            }
        },

        /**
         * Stylize the slider
         */
        stylize: function () {
            var _this = this;

            $(this.element).children('ul').children('li').each(function () {
                var bg = $(this).data('x-slider-image');
                var id = $(this).attr('id');

                // Configure the background image on each li
                _this.set_background_image(id, bg);

                // Reduce de image brightness for better readability
                _this.reduce_brightness(_this.settings, id);
            });
        },

        /**
         * Configure the slider image as a background
         *
         * @param id
         * @param bg
         */
        set_background_image: function (id, bg) {
            $('#' + id + ":before").addRule({
                content: "",
                width: "100%",
                height: "100%",
                "z-index": -1,
                position: "absolute",
                left: 0,
                right: 0,
                display: "block",
                background: "url('" + bg + "') center center",
                "-webkit-background-size": "cover",
                "-moz-background-size": "cover",
                "-o-background-size": "cover",
                "background-size": "cover"
            });
        },
        /**
         * Reduce background image brightness
         *
         *
         * @param settings
         * @param id
         */
        reduce_brightness: function (settings, id) {
            if (this.settings.brightness) {
                $('#' + id + ":before")
                    .addRule({
                        "filter": "brightness(" + this.settings.brightness + ");",
                        "ms-filter": "brightness(" + this.settings.brightness + ");",
                        "moz-filter": "brightness(" + this.settings.brightness + ");",
                        "-webkit-filter": "brightness(" + this.settings.brightness + ");"
                    });
            }
        },
        /**
         * Attatch the unslider on the main element
         */
        attatch: function () {
            $(this.element).unslider({
                speed: 500,
                delay: this.settings.timeout,
                keys: true,
                dots: this.settings.bullets,
                fluid: true
            });
        }
    });

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})
(jQuery, window, document);

$(".x-slider").xSlider();