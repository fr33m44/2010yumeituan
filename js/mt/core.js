var MT = window.MT || {};
MT.namespace = function () {
    var a = arguments,
    o = null,
    i, j, d;
    for (i = 0; i < a.length; ++i) {
	d = a[i].split(".");
	o = MT;
	for (j = (d[0] == "MT") ? 1 : 0; j < d.length; ++j) {
	    o[d[j]] = o[d[j]] || {};
	    o = o[d[j]];
	}
    }
    return o;
};
MT.namespace("util", "app", "widget");
(function () {
    var $C = YAHOO.util.Connect;
    var $D = YAHOO.util.Dom;
    var $E = YAHOO.util.Event;
    var _9 = YAHOO.util.KeyListener;
    var _a = YAHOO.env.ua.ie;
    var _b = (_a == 6);
    var _c = YAHOO.env.ua.gecko;
    var _d = YAHOO.env.ua.webkit;
    var _e = YAHOO.env.ua.opera;
    var _f = YAHOO.lang.isString;
    var _10 = YAHOO.lang.trim;
    MT.util = {
	fixTextareaCursorPosition: function (_11) {
	    if (_f(_11)) {
		_11 = $D.get(_11);
	    }
	    if (_a || _e) {
		var rng = _11.createTextRange();
		rng.text = _11.value;
		rng.collapse(false);
	    } else {
		if (_d) {
		    _11.select();
		    window.getSelection().collapseToEnd();
		}
	    }
	},
	focusTextarea: function (_13) {
	    try {
		_13.focus();
		this.fixTextareaCursorPosition(_13);
	    } catch (e) {}
	},
	checkCapsLock: function (e) {
	    e = (e) ? e : window.event;
	    var _15 = false;
	    if (e.which) {
		_15 = e.which;
	    } else {
		if (e.keyCode) {
		    _15 = e.keyCode;
		}
	    }
	    var _16 = false;
	    if (e.shiftKey) {
		_16 = e.shiftKey;
	    } else {
		if (e.modifiers) {
		    _16 = !! (e.modifiers & 4);
		}
	    }
	    if (_15 >= 97 && _15 <= 122 && _16) {
		return true;
	    }
	    if (_15 >= 65 && _15 <= 90 && !_16) {
		return true;
	    }
	    return false;
	},
	getEvalRes: function (o, _18) {
	    try {
		return eval("(" + o.responseText + ")");
	    } catch (ex) {
		return {
		    "status": 0,
		    "msg": _18 || "\u7f51\u7edc\u6709\u95ee\u9898\uff0c\u8bf7\u7a0d\u540e\u91cd\u8bd5\u3002"//网络有问题，请稍后重试。
		};
	    }
	},
	decodeJSON: function (str) {
	    try {
		return eval("(" + str + ")");
	    } catch (ex) {
		return null;
	    }
	},
	ctrlEnter: function (el, _1b) {
	    var _1c = new YAHOO.util.KeyListener(el, {
		ctrl: true,
		keys: [13, 100]
	    }, function () {
		_1b();
		_1c.disable();
		window.setTimeout(function () {
		    _1c.enable();
		}, 2000);
	    });
	    _1c.enable();
	},
	yFadeRemove: function (_1d, _1e) {
	    var p = $D.getAncestorByTagName(_1d, _1e);
	    if (!p) {
		return;
	    }
	    MT.util.Effect.yellowFadeOut(p, function (el) {
		el.parentNode.removeChild(el);
		if (_a) {
		    MT.util.reFlow();
		}
	    });
	},
	reFlow: function () {
	    if (_a) {
		document.body.style.zoom = 1.1;
		document.body.style.zoom = "";
	    }
	},
	getLength: function (str) {
	    if (!str) {
		return 0;
	    }
	    for (var i = 0, _23 = 0, len = str.length; i < len; i++) {
		_23 = str.charCodeAt(i) > 255 ? _23 + 2 : _23 + 1;
	    }
	    return _23;
	},
	toFull: function (str) {
	    var ret = "";
	    for (var i = 0, len = str.length; i < len; i++) {
		var _29 = str.charCodeAt(i);
		if (_29 == 32) {
		    ret += String.fromCharCode(12288);
		} else {
		    if (_29 < 127) {
			ret += String.fromCharCode(_29 + 65248);
		    } else {
			ret += str[i];
		    }
		}
	    }
	    return ret;
	},
	toHalf: function (str) {
	    var ret = "";
	    for (var i = 0, len = str.length; i < len; i++) {
		var _2e = str.charCodeAt(i);
		if (_2e == 12288) {
		    ret += String.fromCharCode(32);
		} else {
		    if (_2e > 65280 && _2e < 65375) {
			ret += String.fromCharCode(_2e - 65248);
		    } else {
			ret += String.fromCharCode(_2e);
		    }
		}
	    }
	    return ret;
	},
	toPostData: function (o) {
	    var arr = [];
	    for (var i in o) {
		arr.push(encodeURIComponent(i) + "=" + encodeURIComponent(o[i]));
	    }
	    return arr.join("&");
	},
	parseStr: function (str) {
	    if (!str) {
		return null;
	    }
	    var arr = str.split("&");
	    var ret = {};
	    for (var i = 0, len = arr.length; i < len; i++) {
		var tmp = arr[i].split("=");
		ret[tmp[0]] = tmp[1];
	    }
	    return ret;
	},
	loadImage: function (src) {
	    var img = new Image();
	    img.src = src;
	},
	pad: function (num, _3b, _3c) {
	    if (!_3c) {
		_3c = "0";
	    }
	    var len = num.toString().length;
	    while (len < _3b) {
		num = _3c + num;
		len++;
	    }
	    return num;
	},
	parseDate: function (_3e) {
	    var tmp = _3e.split(" ");
	    var _40 = tmp[1] + " " + tmp[2] + ", " + tmp[5] + " " + tmp[3];
	    return Date.parse(_40) - (new Date()).getTimezoneOffset() * 60 * 1000;
	},
	getRelativeTime: function (_41) {
	    var _42 = _f(_41) ? this.parseDate(_41).valueOf() : _41;
	    var now = new Date();
	    var _44 = parseInt((now.getTime() - _42) / 1000, 10);
	    if (_44 < 1) {
		return "1 \u79d2\u524d";
	    } else {
		if (_44 < 60) {
		    return _44 + " \u79d2\u524d";
		} else {
		    if (_44 < 60 * 60) {
			return parseInt(_44 / 60, 10) + " \u5206\u949f\u524d";
		    } else {
			if (_44 < 60 * 60 * 24) {
			    return "\u7ea6 " + parseInt(_44 / 3600, 10) + " \u5c0f\u65f6\u524d";
			} else {
			    return this.getFullTime(_41);
			}
		    }
		}
	    }
	},
	getFullTime: function (_45) {
	    var d = new Date(_f(_45) ? this.parseDate(_45) : _45);
	    var _47 = d.getFullYear();
	    var _48 = this.pad(d.getMonth() + 1, 2);
	    var _49 = this.pad(d.getDate(), 2);
	    var _4a = this.pad(d.getHours(), 2);
	    var _4b = this.pad(d.getMinutes(), 2);
	    return _47 + "-" + _48 + "-" + _49 + " " + _4a + ":" + _4b;
	},
	getText: function (el) {
	    if (_f(el)) {
		el = $D.get(el);
	    }
	    var _4d = el.innerText || el.textContent;
	    return _10(_4d);
	},
	getRadioValue: function (_4e, _4f) {
	    if (_f(_4e)) {
		_4e = $D.get(_4e);
	    }
	    var r = _4e[_4f];
	    if (!r) {
		return null;
	    }
	    var n = r.length;
	    if (n) {
		for (var i = 0; i < n; ++i) {
		    if (r[i].checked) {
			return r[i].value;
		    }
		}
	    }
	    return r.value;
	},
	fixIE6Png: function (_53) {
	    if (!_b) {
		return;
	    }
	    if (_f(_53)) {
		_53 = $D.get(_53);
	    }
	    var reg = /.png$/i;
	    $D.getElementsBy(function (img) {
		return reg.test(img.src);
	    }, "img", _53, function (img) {
		img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\"" + img.src + "\", sizingMethod=\"crop\");";
		img.src = "/static/importantblk.gif";
	    });
	},
	inArray: function (_57, _58, _59) {
	    var key = "",
	    _5b = !! _59;
	    if (_5b) {
		for (key in _58) {
		    if (_58[key] === _57) {
			return true;
		    }
		}
	    } else {
		for (key in _58) {
		    if (_58[key] == _57) {
			return true;
		    }
		}
	    }
	    return false;
	},
	ucfirst: function (str) {
	    str += "";
	    return str.charAt(0).toUpperCase() + str.substr(1);
	},
	uuid: function (len, _5e) {
	    var _5f = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz".split("");
	    var _60 = _5f,
	    _61 = [],
	    i;
	    _5e = _5e || _60.length;
	    if (len) {
		for (i = 0; i < len; i++) {
		    _61[i] = _60[0 | Math.random() * _5e];
		}
	    } else {
		var r;
		_61[8] = _61[13] = _61[18] = _61[23] = "-";
		_61[14] = "4";
		for (i = 0; i < 36; i++) {
		    if (!_61[i]) {
			r = 0 | Math.random() * 16;
			_61[i] = _60[(i == 19) ? (r & 3) | 8 : r];
		    }
		}
	    }
	    return _61.join("");
	},
	getCommonDialog: function (id, _65) {
	    var _66 = {
		width: "400px",
		fixedcenter: true,
		close: false,
		draggable: false,
		zindex: 7,
		modal: true,
		visible: false
	    };
	    for (var i in _65) {
		_66[i] = _65[i];
	    }
	    var _68 = new YAHOO.widget.Panel(id, _66);
	    _68.showEvent.subscribe(function () {
		$D.getElementsByClassName("close", "*", id, function (el) {
		    $E.on(el, "click", function () {
			_68.hide();
		    });
		});
	    });
	    _68.setContent = function (_6a) {
		this.setBody("<div class=\"common-dialog\">" + _6a + "</div>");
	    };
	    _68.fixOverlay = function () {
		if (_b || (_a == 7 && document.compatMode == "BackCompat")) {
		    this.sizeUnderlay();
		}
	    };
	    _68.open = function () {
		this.render(document.body);
		this.show();
	    };
	    return _68;
	},
	Effect: {
	    _yfade: function (el, _6c, _6d, _6e) {
		var a = arguments,
		f = "#fff478",
		t = "#ffffff",
		cb;
		if (YAHOO.lang.isArray(a[2])) {
		    f = a[2][0] ? a[2][0] : f;
		    t = a[2][1] ? a[2][1] : t;
		} else {
		    if (YAHOO.lang.isFunction(a[2])) {
			cb = a[2];
		    }
		}
		if (YAHOO.lang.isFunction(a[3])) {
		    cb = a[3];
		}
		var opa = _6c == "out" ? {
		    from: 1,
		    to: 0
		} : {
		    from: 0,
		    to: 1
		};
		var _74 = new YAHOO.util.ColorAnim(el, {
		    backgroundColor: {
			from: f,
			to: t
		    },
		    opacity: opa
		}, 1, YAHOO.util.Easing.easeNone);
		if ("undefined" != typeof cb) {
		    _74.onComplete.subscribe(function () {
			cb(el);
		    });
		}
		_74.animate();
		return _74;
	    },
	    yellowFadeOut: function (el, _76, _77) {
		return MT.util.Effect._yfade(el, "out", _76, _77);
	    },
	    yellowFadeIn: function (el, _79, _7a) {
		return MT.util.Effect._yfade(el, "in", _79, _7a);
	    }
	},
	Cookie: {
	    getExpiresDate: function (_7b, _7c, _7d) {
		var _7e = new Date();
		if (typeof _7b == "number" && typeof _7c == "number" && typeof _7c == "number") {
		    _7e.setDate(_7e.getDate() + parseInt(_7b, 10));
		    _7e.setHours(_7e.getHours() + parseInt(_7c, 10));
		    _7e.setMinutes(_7e.getMinutes() + parseInt(_7d, 10));
		    return _7e.toGMTString();
		}
	    },
	    _getValue: function (_7f) {
		var _80 = document.cookie.indexOf(";", _7f);
		if (_80 == -1) {
		    _80 = document.cookie.length;
		}
		return unescape(document.cookie.substring(_7f, _80));
	    },
	    get: function (_81) {
		var arg = _81 + "=";
		var _83 = arg.length;
		var _84 = document.cookie.length;
		var i = 0;
		while (i < _84) {
		    var j = i + _83;
		    if (document.cookie.substring(i, j) == arg) {
			return this._getValue(j);
		    }
		    i = document.cookie.indexOf(" ", i) + 1;
		    if (i === 0) {
			break;
		    }
		}
		return "";
	    },
	    set: function (_87, _88, _89, _8a, _8b, _8c) {
		document.cookie = _87 + "=" + escape(_88) + ((_89) ? "; expires=" + _89 : "") + ((_8a) ? "; path=" + _8a : "") + ((_8b) ? "; domain=" + _8b : "") + ((_8c) ? "; secure" : "");
	    },
	    remove: function (_8d, _8e, _8f) {
		if (this.get(_8d)) {
		    document.cookie = _8d + "=" + ((_8e) ? "; path=" + _8e : "") + ((_8f) ? "; domain=" + _8f : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
		}
	    },
	    clear: function () {
		var _90 = document.cookie.split(";");
		for (var i = 0; i < _90.length; i++) {
		    this.remove(_10(_90[i].split("=")[0]), "/", "mt.com");
		    this.remove(_10(_90[i].split("=")[0]), "/", "kxapp.mt.com");
		}
	    }
	}
    };
    MT.widget = {
	selectAndCopy: function (_92, _93, _94) {
	    if (_f(_92)) {
		_92 = $D.get(_92);
	    }
	    if (!_92) {
		return;
	    }
	    function copy() {
		if (_a) {
		    try {
			window.clipboardData.setData("Text", _92.value);
			alert(_94 || "\u590d\u5236\u6210\u529f\uff0c\u4f60\u53ef\u4ee5\u7c98\u5e16\u5230MSN\u6216QQ\u4e2d\u53d1\u7ed9\u597d\u53cb\u3002");
		    } catch (e) {}
		}
		window.setTimeout(function () {
		    _92.select();
		}, 0);
	    }
	    $E.on(_92, "click", copy);
	    $E.on(_93, "click", copy);
	},
	showMessage: function (_95, _96) {
	    var _97 = $D.getElementsByClassName("sysmsgw", "div", "doc");
	    if (_97.length) {
		_97 = _97[0];
	    } else {
		_97 = document.createElement("div");
		_97.className = "sysmsgw";
		_97.innerHTML = "<div class=\"sysmsg\"><p></p><span class=\"close\" onclick=\"MT.app.Core.removeFromCloseButton(this)\">\u5173\u95ed</span></div>";
		$D.insertAfter(_97, "hdw");
	    }
	    _97.id = "sysmsg-" + _95;
	    var p = _97.getElementsByTagName("p")[0];
	    p.innerHTML = _96;
	},
	removeMessage: function () {
	    var _99 = $D.getElementsByClassName("sysmsgw", "div", "doc");
	    if (_99.length) {
		_99 = _99[0];
		_99.parentNode.removeChild(_99);
	    }
	},
	startMsnChat: function (_9a) {
	    try {
		window.setTimeout(function () {
		    var frm = document.createElement("form");
		    $D.get("doc").appendChild(frm);
		    frm.method = "post";
		    frm.action = "msnim:chat?contact=" + _9a;
		    frm.submit();
		}, 100);
	    } catch (e) {
		alert(e);
	    }
	}
    };
    MT.app.Core = {
	removeFromCloseButton: function (_9c) {
	    var p = $D.getAncestorByClassName(_9c, "sysmsgw");
	    p.parentNode.removeChild(p);
	},
	removeTip: function () {
	    var _9e = $D.get("sysmsg-tip");
	    var _9f = $D.get("sysmsg-tip-close");
	    if (!_9e || !_9f) {
		return;
	    }
	    $E.on(_9f, "click", function (e) {
		_9e.parentNode.removeChild(_9e);
	    });
	},
	sysmsg: function () {
	    this.removeTip();
	    var _a1 = $D.get("sysmsg-error");
	    if (!_a1) {
		_a1 = $D.get("sysmsg-success");
	    }
	    if (!_a1) {
		return;
	    }
	    $D.getElementsByClassName("close", "span", _a1, function (el) {
		$E.on(el, "click", function (e) {
		    MT.app.Core.removeFromCloseButton(el);
		});
	    });
	},
	initAccountMenu: function () {
	    var _a4 = $D.get("myaccount");
	    var _a5 = $D.get("myaccount-menu");
	    $E.on(_a4, "click", function (e) {
		_a4.blur();
		var _a7 = this.getAttribute("isuc");
		if (!_a7) {
		    $E.stopEvent(e);
		}
	    });
	    $E.on(_a4, "mouseover", function (e) {
		_a5.style.display = "block";
	    });
	    $E.on(_a4, "mouseout", function (e) {
		handleOut(e);
	    });
	    $E.on(_a5, "mouseout", function (e) {
		handleOut(e);
	    });

	    function handleOut(e) {
		var _ac = $E.getTarget(e);
		var _ad = $E.getRelatedTarget(e);
		if (_a5 == _ad || $D.isAncestor(_a5, _ad)) {
		    $D.addClass(_a4, "hover");
		} else {
		    $D.removeClass(_a4, "hover");
		    _a5.style.display = "none";
		}
	    }
	},
	changeCity: function () {
	    var _ae = $D.get("header-city");
	    var _af = $D.get("guides-city-list");
	    if (!_ae || !_af) {
		return;
	    }
	    $E.on(_ae, "click", function (e) {
		if ($D.getStyle(_af, "display") == "none") {
		    _ae.className = "unfold";
		    _af.style.display = "block";
		} else {
		    _ae.className = "fold";
		    _af.style.display = "none";
		}
	    });
	    $E.on(document, "click", function (e) {
		var _b2 = $E.getTarget(e);
		var _b3 = _b2.nodeName.toLowerCase();
		if (_b2 != _ae && _b2 != _af && !$D.isAncestor(_ae, _b2) && !$D.isAncestor(_af, _b2)) {
		    window.setTimeout(function () {
			_ae.className = "fold";
			_af.style.display = "none";
		    }, 10);
		}
	    });
	},
	msnHelp: function () {
	    var _b4 = "custom-service-dialog";
	    var _b5 = MT.util.getCommonDialog(_b4, {
		width: "420px"
	    });
	    var _b6 = null;
	    var _b7 = 15;
	    $E.on(["service-msn-help", "service-qq-help"], "click", function (e) {
		$E.stopEvent(e);
		this.blur();
		var _b9 = this.getAttribute("stype");
		var msn = this.getAttribute("saddress");
		if (_a) {
		    if (_b9 == "msn") {
			MT.widget.startMsnChat(msn);
		    } else {
			if (_b9 == "qq") {
			    var url = this.getAttribute("surl");
			    window.open(url, "_blank", "height=544, width=644,toolbar=no,scrollbars=no,menubar=no,status=no");
			}
		    }
		} else {
		    showDialog(_b9, msn);
		}
	    });

	    function showDialog(_bc, _bd) {
		var _be = ["<div class=\"\">"];
		_be.push("<h3 class=\"head\">\u5ba2\u670d" + (_bc == "msn" ? "MSN" : "QQ") + "<span class=\"close\">\u5173\u95ed</span></h3>");
		_be.push("<div class=\"body\">");
		if (_bc == "msn") {
		    _be.push("<p>\u62b1\u6b49\uff0c\u5f53\u524d\u6d4f\u89c8\u5668\u4e0d\u517c\u5bb9\u5ba2\u670dMSN\u3002<br />\u60a8\u53ef\u4ee5\u52a0 <strong>" + _bd + "</strong> \u4e3a\u597d\u53cb\uff0c\u518d\u8fdb\u884c\u54a8\u8be2\u3002</p>");
		} else {
		    _be.push("<p>\u62b1\u6b49\uff0c\u5f53\u524d\u6d4f\u89c8\u5668\u4e0d\u517c\u5bb9\u5ba2\u670dQQ\u3002<br />\u60a8\u53ef\u4ee5\u52a0 <strong>" + _bd + "</strong> \u4e3a\u597d\u53cb\uff0c\u518d\u8fdb\u884c\u54a8\u8be2\u3002</p>");
		}
		_be.push("</div></div>");
		$D.addClass(document.body, "yui-skin-sam");
		_b5.hideEvent.subscribe(function () {
		    window.clearTimeout(_b6);
		});
		_b5.setContent(_be.join(""));
		_b5.open();
		_b6 = window.setTimeout(function () {
		    _b5.hide();
		}, _b7 * 1000);
	    }
	},
	trackGAEvent: function () {
	    var _bf = "gaevent";
	    var _c0 = "galabel";
	    $E.on(document, "click", function (e) {
		var _c2 = $E.getTarget(e);
		var _c3 = _c2.nodeName.toLowerCase();
		trackCustomEvent(_c2);
		if (_c3 === "a") {
		    trackOutLink(_c2);
		}
	    });

	    function trackCustomEvent(el) {
		var _c5 = el.getAttribute(_bf);
		if (!_c5) {
		    return;
		}
		var _c6 = _c5.split("|");
		var _c7 = _c6[0];
		var _c8 = _c6[1];
		var _c9 = _c6[2];
		var _ca = _c6[3];
		if (!_c9 && el.href) {
		    _c9 = el.href.replace("http://", "");
		}
	    }
	    function trackOutLink(el) {
		var _cc = el.href;
		var reg = /^(http:\/\/|mailto:)/;
		if (_cc && reg.test(_cc)) {
		    _cc = _cc.replace("http://", "");
		    var _ce = _cc.indexOf("?");
		    var _cf = el.getAttribute(_c0);
		    if (_ce > 0) {
			_cc = _cc.substring(0, _ce);
		    }
		    if (/\.(meituan|mt|fdpmt)\.com/.test(_cc)) {
			return;
		    }
		    if (_cf) {
			_cc += "#label=" + _cf;
		    }
		}
	    }
	},
	_subscribedDialog: null,
	getSubscribedDialog: function () {
	    if (!this._subscribedDialog) {
		var _d0 = {
		    width: "400px",
		    fixedcenter: true,
		    close: false,
		    draggable: false,
		    zindex: 7,
		    modal: true,
		    visible: false
		};
		this._subscribedDialog = new YAHOO.widget.Panel("email-subscribed-dialog", _d0);
	    }
	    return this._subscribedDialog;
	},
	startSubscribedDialog: function (_d1, _d2) {
	    var _d3 = this;
	    $D.addClass(document.body, "yui-skin-sam");
	    showDialog();

	    function showDialog() {
		var _d4 = _d3.getSubscribedDialog();
		var _d5 = getHelpType(_d1);
		var _d6 = ["<div class=\"email-subscribed-dialog-c\">"];
		_d6.push("<h3><span id=\"email-subscribed-dialog-close\" class=\"close\">\u5173\u95ed</span></h3>");
		_d6.push("<div class=\"succ\">");
		_d6.push("<p class=\"title\">\u90ae\u4ef6\u8ba2\u9605\u6210\u529f\uff01</p>");
		if (_d2) {
		    _d6.push("<p class=\"notice\">" + _d2 + "\u6bcf\u5929\u7684\u56e2\u8d2d\u5c06\u53ca\u65f6\u53d1\u5230\u60a8\u90ae\u7bb1</p>");
		}
		_d6.push("</div>");
		_d6.push("<div class=\"setting\"><h4>\u60a8\u53ef\u80fd\u6536\u4e0d\u5230\u8ba2\u9605\u90ae\u4ef6...</h4><p class=\"text\">\u8bf7\u5c06 <strong>123@yumeituan.com</strong> \u52a0\u5165\u90ae\u7bb1\u767d\u540d\u5355</p>");
		if (_d5) {
		    _d6.push("<a class=\"link\" href=\"/help.php?id=" + _d5 + "\" target=\"_blank\">\u7acb\u5373\u8fdb\u884c\u8bbe\u7f6e</a>");
		}
		_d6.push("</div>");
		_d6.push("</div>");
		_d4.setBody(_d6.join(""));
		_d4.render(document.body);
		_d4.show();
		$E.on("email-subscribed-dialog-close", "click", function (e) {
		    _d4.hide();
		});
	    }
	    function getHelpType(_d8) {
		var _d9 = {
		    "163.com": "58",
		    "126.com": "58",
		    "gmail.com": "60",
		    "qq.com": "59",
		    "vip.qq.com": "59",
		    "foxmail.com": "59",
		    "live.com": "61",
		    "live.cn": "61",
		    "hotmail.com": "61",
		    "yahoo.com.cn": "63",
		    "yahoo.cn": "63",
		    "sina.com": "62",
		    "vip.sina.com": "62",
		    "sohu.com": "64",
		    "139.com": "65"
		};
		var _da, key;
		if (_d8) {
		    _da = _d8.indexOf("@");
		    if (_da) {
			key = _d8.substr(_da + 1);
			if (_d9[key]) {
			    return _d9[key];
			}
		    }
		}
		return "other";
	    }
	},
	subscribe: function (_dc, _dd, _de) {
	    _dc = $D.get(_dc);
	    if (!_dc) {
		return;
	    }
	    var _df = _dc.email;
	    var _e0 = "\u8bf7\u8f93\u5165\u60a8\u7684Email...";//请输入您的Email...
	    if (_df && _df.value !== "") {
		_e0 = _df.value;
	    }
	    if (_df.value === "") {
		_df.style.color = "#999999";
		_df.value = _e0;
	    }
	    $E.on(_dc, "submit", function (e) {
		$E.stopEvent(e);
		if (_df.value === "" || _df.value === _e0) {
		    return alert("\u8bf7\u8f93\u5165\u60a8\u7684Email\u5730\u5740\u3002");//请输入您的Email地址。
		}
		$C.setForm(_dc);
		var _e2 = $C.asyncRequest("POST", _dc.action, {
		    success: function (o) {
			var res = MT.util.getEvalRes(o);
			if (res.status) {
			    MT.app.Core.startSubscribedDialog(_df.value, _dd);
			} else {
			    if (res.data && res.data.needCaptcha) {
				window.location.href = "/maillist/subscribe/" + _de;
			    } else {
				alert(res.msg);
			    }
			}
		    },
		    failure: function (o) {}
		});
		return true;
	    });
	    $E.on(_df, "focus", function (e) {
		if (_df.value === _e0) {
		    _df.value = "";
		}
		_df.style.color = "#000000";
	    });
	    $E.on(_df, "blur", function (e) {
		if (_df.value === "") {
		    _df.style.color = "#999999";
		    _df.value = _e0;
		}
	    });
	},
	headerSubscribe: function () {
	    var _e8 = $D.get("header-city");
	    if (!_e8) {
		return;
	    }
	    var id = _e8.getAttribute("sid");
	    var _ea = _e8.getAttribute("sname");
	    var _eb = _e8.getAttribute("sslug");
	    this.subscribe("header-subscribe-form", _ea, _eb);
	    this.subscribeSms("header-subscribe-sms", id, _ea);
	},
	/*取消短信订阅*/
	headerUnsubscribe: function () {
	    var _e8 = $D.get("header-city");
	    if (!_e8) {
		return;
	    }
	    var id = _e8.getAttribute("sid");
	    var _ea = _e8.getAttribute("sname");
	    var _eb = _e8.getAttribute("sslug");
	    //this.subscribe("header-subscribe-form", _ea, _eb);
	    this.unsubscribeSms("header-unsubscribe-sms", id, _ea);
	},
	autoCompleteEmail: function (_ec, _ed) {
	    if (_f(_ec)) {
		_ec = $D.get(_ec);
	    }
	    if (_f(_ed)) {
		_ed = $D.get(_ed);
	    }
	    _ed.innerHTML = "<p class=\"email-title\">\u8bf7\u9009\u62e9\u60a8\u7684\u90ae\u7bb1\u7c7b\u578b...</p><ul class=\"email-list\"></ul>";
	    var _ee = _ed.getElementsByTagName("ul")[0];
	    var _ef = ["163.com", "gmail.com", "qq.com", "126.com", "hotmail.com", "sina.com", "yahoo.com.cn", "sohu.com", "yahoo.cn", "139.com"];
	    var _f0 = 0;
	    var _f1 = 0;
	    $E.on(_ec, "blur", function () {
		$D.setStyle(_ed, "display", "none");
	    });
	    $E.on(_ec, "keydown", function (ev) {
		var key = ev.keyCode;
		if (key == 9) {
		    if ($D.getStyle(_ed, "display") === "block") {
			$D.setStyle(_ed, "display", "none");
		    }
		} else {
		    if (key == 13) {
			_ec.value = select();
			if ($D.getStyle(_ed, "display") === "block") {
			    $D.setStyle(_ed, "display", "none");
			    $E.stopEvent(ev);
			}
		    } else {
			if (key == 38 || key == 40) {
			    if (ev.shiftKey == 1) {
				return;
			    }
			    if (key == 38) {
				if (_f0 <= 0) {
				    _f0 = _f1;
				}
				_f0--;
				selectLi(_f0);
				$E.stopEvent(ev);
			    } else {
				if (key == 40) {
				    if (_f0 > _f1 - 2) {
					_f0 = -1;
				    }
				    _f0++;
				    selectLi(_f0);
				    $E.stopEvent(ev);
				}
			    }
			} else {
			    window.setTimeout(function () {
				displayList(_ec);
			    }, 10);
			}
		    }
		}
	    });

	    function displayList() {
		var _f4 = _ec.value;
		var _f5 = [];
		var _f6 = "";
		var _f7 = "";
		var _f8 = _f4.indexOf("@");
		if (_f8 < 0) {
		    _f7 = _f4;
		} else {
		    if (_f8 == _f4.length - 1) {
			_f7 = _f4.substr(0, _f8);
		    } else {
			_f7 = _f4.substr(0, _f8);
			_f6 = _f4.substr(_f8 + 1);
		    }
		}
		_f5.push("<li>" + _f4 + "</li>");
		for (var i = 0; i < _ef.length; i++) {
		    var _fa = _ef[i];
		    var _fb = _ef[i].substr(0, _f6.length);
		    if (_f6 === "" || _f6 == _fb) {
			var _fc = _f7 + "@" + _fa;
			_f5.push("<li title=\"" + _fc + "\">" + _fc + "</li>");
		    }
		}
		_f0 = 0;
		removeEvent();
		_ee.innerHTML = _f5.join("");
		$D.setStyle(_ed, "display", "block");
		attachEvent();
	    }
	    function removeEvent() {
		var _fd = _ee.getElementsByTagName("li");
		for (var i = 0; i < _fd.length; i++) {
		    var el = _fd[i];
		    el.onmousedown = null;
		    el.onmouseover = null;
		    el.onmouseout = null;
		}
	    }
	    function attachEvent() {
		var _100 = _ee.getElementsByTagName("li");
		var _101 = _100[0];
		_f1 = _100.length;
		$D.addClass(_101, "current");
		for (var i = 0; i < _100.length; i++) {
		    var el = _100[i];
		    el.onmousedown = function () {
			_ec.value = this.innerHTML;
		    };
		    el.onmouseover = function () {
			$D.removeClass(_101, "current");
			$D.addClass(this, "current");
		    };
		    el.onmouseout = function () {
			$D.removeClass(this, "current");
			$D.addClass(_101, "current");
		    };
		}
	    }
	    function selectLi(_104) {
		var _105 = _ee.getElementsByTagName("li");
		for (var i = 0; i < _105.length; i++) {
		    $D.removeClass(_105[i], "current");
		}
		$D.addClass(_105[_104], "current");
	    }
	    function select() {
		var _107 = _ee.getElementsByTagName("li");
		if (_107[_f0]) {
		    return _107[_f0].innerHTML;
		} else {
		    return _107[0].innerHTML;
		}
	    }
	},
	init: function () {
	    this.sysmsg();
	    this.initAccountMenu();
	    this.changeCity();
	    this.headerSubscribe();
	    this.headerUnsubscribe();
	    this.autoCompleteEmail("header-subscribe-email", "header-subscribe-auto");
	    this.trackGAEvent();
	    this.msnHelp();
	}
    };
    MT.app.Core.subscribeSms = function (el, _109, _10a) {
	if (_f(el)) {
	    el = $D.get(el);
	}
	if (!_10a) {
	    _10a = "\u7f8e\u56e2";
	}
	var _10b = "sms-subscribe-dialog";
	var _10c = MT.util.getCommonDialog(_10b, {
	    width: "400px"
	});
	var _10d = false;
	var _10e = "<h4>\u77ed\u4fe1\u8ba2\u9605" + _10a + "\u6bcf\u65e5\u56e2\u8d2d\u4fe1\u606f</h4><p class=\"noti\">\u8ba4\u8bc1\u7801\u5df2\u53d1\u9001\u5230\u60a8\u7684\u624b\u673a\uff1a<strong>%mobile</strong></p><p class=\"verify cf\"><label for=\"sms-subscribe-verify\">\u8ba4\u8bc1\u7801</label><input type=\"text\" id=\"sms-subscribe-verify\" class=\"f-text\" maxlength=\"20\" name=\"code\" /><span class=\"tip\">\u8bf7\u8f93\u5165\u60a8\u6536\u5230\u7684\u8ba4\u8bc1\u7801\uff0c\u5b8c\u6210\u8ba2\u9605</span></p><p class=\"act\"><input type=\"hidden\" name=\"act\" value=\"add_mobile\" /><input type=\"hidden\" name=\"do\" value=\"add\" /><input type=\"hidden\" name=\"cityid\" value=\"%cityId\" /><input type=\"hidden\" name=\"mobile\" value=\"%mobile\" /><input type=\"submit\" class=\"commit confirm\" value=\"\u53d1\u9001\" /></p>";
	var _10f = "<div class=\"succ\"><p class=\"title\">\u77ed\u4fe1\u8ba2\u9605\u6210\u529f\uff01</p><p class=\"notice\">" + _10a + "\u6bcf\u5929\u7684\u56e2\u8d2d\u4fe1\u606f\u5c06\u53ca\u65f6\u53d1\u5230\u60a8\u624b\u673a</p></div><div class=\"alert\">\u6e29\u99a8\u63d0\u793a\uff1a\u6536\u5230\u8ba2\u9605\u77ed\u4fe1\u540e\uff0c\u76f4\u63a5\u56de\u590dQX\u53ef\u53d6\u6d88\u8ba2\u9605</div>";
	$E.on(el, "click", function (e) {
	    $D.addClass(document.body, "yui-skin-sam");
	    showDialog();
	});

	function showDialog() {
	    _10d = false;
	    var html = ["<div class=\"\">"];
	    html.push("<h3 class=\"head\"><span class=\"close\">\u5173\u95ed</span></h3>");
	    html.push("<div class=\"body\"><form id=\"sms-subscribe-form\" action=\"/subscribe.php\" method=\"post\">");
	    html.push("<h4>\u77ed\u4fe1\u8ba2\u9605" + _10a + "\u6bcf\u65e5\u56e2\u8d2d\u4fe1\u606f</h4>");
	    html.push("<p class=\"mobile\"><label for=\"sms-subscribe-mobile\">\u624b\u673a\u53f7</label><input type=\"text\" id=\"sms-subscribe-mobile\" class=\"f-text\" maxlength=\"20\" name=\"mobile\" value=\"\" /><span class=\"tip\">\u8bf7\u8f93\u5165\u60a8\u7684\u624b\u673a\u53f7</span></p>");
	    html.push("<p class=\"enter\"><label for=\"sms-subscribe-captcha\">\u9a8c\u8bc1\u7801</label><input type=\"text\" id=\"sms-subscribe-captcha\" class=\"f-text\" maxlength=\"20\" name=\"captcha\" /><span class=\"tip\">\u6309\u4e0b\u56fe\u5b57\u7b26\u586b\u5199\uff0c\u4e0d\u533a\u5206\u5927\u5c0f\u5199</span></p>");
	    html.push("<p class=\"captcha cf\"><img id=\"sms-subscribe-captcha-img\" src=\"captcha.php?" + Math.random() + "\" width=\"100\" height=\"20\" /><span id=\"sms-subscribe-captcha-change\">\u770b\u4e0d\u6e05\u695a\uff1f\u6362\u4e00\u5f20</span></p>");
	    html.push("<p class=\"act\"><input type=\"hidden\" name=\"act\" value=\"add_mobile\" /><input type=\"hidden\" name=\"do\" value=\"add\" /><input type=\"hidden\" name=\"cityid\" value=\"" + _109 + "\" /><input type=\"submit\" class=\"commit\" value=\"\u53d1\u9001\" /></p>");
	    html.push("</form></div></div>");
	    _10c.setContent(html.join(""));
	    _10c.open();
	    initEvent();
	}
	function initEvent() {
	    $E.on("sms-subscribe-captcha-change", "click", changeCaptcha);
	    $E.on("sms-subscribe-form", "submit", function (e) {
		$E.stopEvent(e);
		if (_10d) {
		    var code = this.code.value;
		    if (code === "") {
			alert("\u8bf7\u8f93\u5165\u60a8\u6536\u5230\u7684\u8ba4\u8bc1\u7801");
			this.code.focus();
			return false;
		    }
		    confirmVerify(this);
		} else {
		    var _114 = this.mobile.value;
		    var _115 = this.captcha.value;
		    if (_114 === "" || !_114.match(/^\d{11}$/)) {
			alert("\u8bf7\u8f93\u5165\u6b63\u786e\u768411\u4f4d\u624b\u673a\u53f7\u7801");
			this.mobile.focus();
			return false;
		    } else {
			if (_115 === "") {
			    alert("\u9a8c\u8bc1\u7801\u4e0d\u80fd\u4e3a\u7a7a");
			    this.captcha.focus();
			    return false;
			}
		    }
		    sendVerify(this);
		}
		return true;
	    });
	}
	function changeCaptcha() {
	    var img = $D.get("sms-subscribe-captcha-img");
	    var src = img.src.replace(/(\?0\.\d+)?$/, "");
	    img.src = src + "?" + Math.random();
	}
	function sendVerify(_118) {
	    $C.setForm(_118);
	    var _119 = _118.cityid.value;
	    var _11a = _118.mobile.value;
	    var _11b = $C.asyncRequest("POST", "subscribe.php", {
		success: function (o) {
		    var res = MT.util.getEvalRes(o);
		    if (res.status) {
			if (res.data["subscribed"]) {
			    showSucc(_118);
			} else {
			    _118.innerHTML = _10e.replace(/%cityId/g, _119).replace(/%mobile/g, _11a);
			    window.setTimeout(function () {
				_118.code.focus();
			    }, 100);
			    _10d = true;
			    fixOverlay();
			}
		    } else {
			changeCaptcha();
			alert(res.msg);
			_118.captcha.focus();
		    }
		},
		failure: function (o) {}
	    });
	}
	function confirmVerify(_11f) {
	    $C.setForm(_11f);
	    var _120 = $C.asyncRequest("POST", "subscribe.php", {
		success: function (o) {
		    var res = MT.util.getEvalRes(o);
		    if (res.status) {
			showSucc(_11f);
		    } else {
			alert(res.msg);
			_11f.code.focus();
		    }
		},
		failure: function (o) {}
	    });
	}
	function showSucc(_124) {
	    _124.innerHTML = _10f;
	    fixOverlay();
	}
	function fixOverlay() {
	    if (_b || (_a == 7 && document.compatMode == "BackCompat")) {
		_10c.sizeUnderlay();
	    }
	}
    };
    MT.app.Core.unsubscribeSms = function (el, _109, _10a) {
	if (_f(el)) {
	    el = $D.get(el);
	}
	if (!_10a) {
	    _10a = "\u7f8e\u56e2";
	}
	var _10b = "sms-subscribe-dialog";
	var _10c = MT.util.getCommonDialog(_10b, {
	    width: "400px"
	});
	var _10d = false;
	var _10e = "<h4>\u53D6\u6D88\u8BA2\u9605</h4><p class=\"noti\">\u8ba4\u8bc1\u7801\u5df2\u53d1\u9001\u5230\u60a8\u7684\u624b\u673a\uff1a<strong>%mobile</strong></p><p class=\"verify cf\"><label for=\"sms-subscribe-verify\">\u8ba4\u8bc1\u7801</label><input type=\"text\" id=\"sms-subscribe-verify\" class=\"f-text\" maxlength=\"20\" name=\"code\" /><span class=\"tip\">\u8bf7\u8f93\u5165\u60a8\u6536\u5230\u7684\u8ba4\u8bc1\u7801</span></p><p class=\"act\"><input type=\"hidden\" name=\"act\" value=\"add_mobile\" /><input type=\"hidden\" name=\"do\" value=\"del\" /><input type=\"hidden\" name=\"cityid\" value=\"%cityId\" /><input type=\"hidden\" name=\"mobile\" value=\"%mobile\" /><input type=\"submit\" class=\"commit confirm\" value=\"\u53d1\u9001\" /></p>";
	var _10f = "<div class=\"succ\"><p class=\"title\">\u77ED\u4FE1\u53D6\u6D88\u8BA2\u9605\u6210\u529F\uFF01</p></div>";
	$E.on(el, "click", function (e) {
	    $D.addClass(document.body, "yui-skin-sam");
	    showDialog();
	});

	function showDialog() {
	    _10d = false;
	    var html = ["<div class=\"\">"];
	    html.push("<h3 class=\"head\"><span class=\"close\">\u5173\u95ed</span></h3>");
	    html.push("<div class=\"body\"><form id=\"sms-subscribe-form\" action=\"/subscribe.php\" method=\"post\">");
	    html.push("<h4>\u53D6\u6D88\u77ED\u4FE1\u8BA2\u9605</h4>");
	    html.push("<p class=\"mobile\"><label for=\"sms-subscribe-mobile\">\u624b\u673a\u53f7</label><input type=\"text\" id=\"sms-subscribe-mobile\" class=\"f-text\" maxlength=\"20\" name=\"mobile\" value=\"\" /><span class=\"tip\">\u8bf7\u8f93\u5165\u60a8\u7684\u624b\u673a\u53f7</span></p>");
	    html.push("<p class=\"enter\"><label for=\"sms-subscribe-captcha\">\u9a8c\u8bc1\u7801</label><input type=\"text\" id=\"sms-subscribe-captcha\" class=\"f-text\" maxlength=\"20\" name=\"captcha\" /><span class=\"tip\">\u6309\u4e0b\u56fe\u5b57\u7b26\u586b\u5199\uff0c\u4e0d\u533a\u5206\u5927\u5c0f\u5199</span></p>");
	    html.push("<p class=\"captcha cf\"><img id=\"sms-subscribe-captcha-img\" src=\"captcha.php?" + Math.random() + "\" width=\"100\" height=\"20\" /><span id=\"sms-subscribe-captcha-change\">\u770b\u4e0d\u6e05\u695a\uff1f\u6362\u4e00\u5f20</span></p>");
	    html.push("<p class=\"act\"><input type=\"hidden\" name=\"act\" value=\"add_mobile\" /><input type=\"hidden\" name=\"do\" value=\"del\" /><input type=\"hidden\" name=\"cityid\" value=\"" + _109 + "\" /><input type=\"submit\" class=\"commit\" value=\"\u53d1\u9001\" /></p>");
	    html.push("</form></div></div>");
	    _10c.setContent(html.join(""));
	    _10c.open();
	    initEvent();
	}
	function initEvent() {
	    $E.on("sms-subscribe-captcha-change", "click", changeCaptcha);
	    $E.on("sms-subscribe-form", "submit", function (e) {
		$E.stopEvent(e);
		if (_10d) {
		    var code = this.code.value;
		    if (code === "") {
			alert("\u8bf7\u8f93\u5165\u60a8\u6536\u5230\u7684\u8ba4\u8bc1\u7801");
			this.code.focus();
			return false;
		    }
		    confirmVerify(this);
		} else {
		    var _114 = this.mobile.value;
		    var _115 = this.captcha.value;
		    if (_114 === "" || !_114.match(/^\d{11}$/)) {
			alert("\u8bf7\u8f93\u5165\u6b63\u786e\u768411\u4f4d\u624b\u673a\u53f7\u7801");
			this.mobile.focus();
			return false;
		    } else {
			if (_115 === "") {
			    alert("\u9a8c\u8bc1\u7801\u4e0d\u80fd\u4e3a\u7a7a");
			    this.captcha.focus();
			    return false;
			}
		    }
		    sendVerify(this);
		}
		return true;
	    });
	}
	function changeCaptcha() {
	    var img = $D.get("sms-subscribe-captcha-img");
	    var src = img.src.replace(/(\?0\.\d+)?$/, "");
	    img.src = src + "?" + Math.random();
	}
	function sendVerify(_118) {
	    $C.setForm(_118);
	    var _119 = _118.cityid.value;
	    var _11a = _118.mobile.value;
	    var _11b = $C.asyncRequest("POST", "subscribe.php", {
		success: function (o) {
		    var res = MT.util.getEvalRes(o);
		    if (res.status) {
			if (res.data["subscribed"]) {
			    showSucc(_118);
			} else {
			    _118.innerHTML = _10e.replace(/%cityId/g, _119).replace(/%mobile/g, _11a);
			    window.setTimeout(function () {
				_118.code.focus();
			    }, 100);
			    _10d = true;
			    fixOverlay();
			}
		    } else {
			changeCaptcha();
			alert(res.msg);
			_118.captcha.focus();
		    }
		},
		failure: function (o) {}
	    });
	}
	function confirmVerify(_11f) {
	    $C.setForm(_11f);
	    var _120 = $C.asyncRequest("POST", "subscribe.php", {
		success: function (o) {
		    var res = MT.util.getEvalRes(o);
		    if (res.status) {
			showSucc(_11f);
		    } else {
			alert(res.msg);
			_11f.code.focus();
		    }
		},
		failure: function (o) {}
	    });
	}
	function showSucc(_124) {
	    _124.innerHTML = _10f;
	    fixOverlay();
	}
	function fixOverlay() {
	    if (_b || (_a == 7 && document.compatMode == "BackCompat")) {
		_10c.sizeUnderlay();
	    }
	}
    };
})();