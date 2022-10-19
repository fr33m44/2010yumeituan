MT.namespace("widget.Rating");
(function () {
    var $C = YAHOO.util.Connect;
    var $D = YAHOO.util.Dom;
    var $E = YAHOO.util.Event;
    var _4 = YAHOO.util.CustomEvent;
    var _5 = YAHOO.util.KeyListener;
    var _6 = YAHOO.env.ua.ie;
    var _7 = (_6 == 6);
    var _8 = YAHOO.env.ua.gecko;
    var _9 = YAHOO.env.ua.webkit;
    var _a = YAHOO.env.ua.opera;
    var _b = YAHOO.lang.isString;
    var _c = YAHOO.lang.trim;
    MT.widget.Rating = function (el, _e) {
        this.elContainer = _b(el) ? $D.get(el) : el;
        this.starClass = "rating-star";
        this.linkClass = "rating-link";
        this.isInProgress = false;
        this.config = {};
        if (_e) {
            for (var _f in _e) {
                this.config[_f] = _e[_f];
            }
        }
        this.onMouseOver = new _4("onMouseOver", this);
        this.onMouseOut = new _4("onMouseOut", this);
        this.beforeSelect = new _4("beforeSelect", this);
        this.onSelect = new _4("onSelect", this);
        this.star = $D.getElementsByClassName(this.starClass, "span", this.elContainer)[0];
        this.links = $D.getElementsByClassName(this.linkClass, "a", this.elContainer);
        this.setState();
        this.attachEvent();
    };
    MT.widget.Rating.prototype = {
        attachEvent: function () {
            var els = this.links;
            var _11 = this;
            for (var i = 0, len = els.length; i < len; i++) {
                $E.on(els[i], "mouseover", function (e) {
                    $E.stopEvent(e);
                    _11.star.className = _11.starClass + " " + _11.starClass + this.innerHTML;
                    _11.onMouseOver.fire(this);
                });
                $E.on(els[i], "mouseout", function (e) {
                    $E.stopEvent(e);
                    _11.star.className = _11.orignalClass;
                    _11.onMouseOut.fire(this);
                });
                $E.on(els[i], "click", this.rate, els[i], this);
                $E.on(els[i], "dblclick", function (e) {
                    $E.stopEvent(e);
                });
            }
        },
        setState: function () {
            var reg = /(\d)$/;
            this.beforeSelect.fire(this.currentScore);
            this.orignalClass = this.star.className;
            if (reg.test(this.orignalClass)) {
                this.currentScore = this.orignalClass.match(reg)[1];
            } else {
                this.currentScore = 0;
            }
            this.onSelect.fire(this.currentScore);
        },
        select: function (_18) {
            this.star.className = this.starClass + " " + this.starClass + _18;
            this.setState();
        },
        rate: function (e, obj) {
            var el = obj,
                _1c = this;
            if (!this.isInProgress) {
                var _1d = el.innerHTML;
                this.select(_1d);
                this.isInProgress = true;
                window.setTimeout(function () {
                    _1c.isInProgress = false;
                }, 100);
            }
        }
    };
})();