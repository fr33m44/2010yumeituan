(function () {
    var $C = YAHOO.util.Connect;
    var $D = YAHOO.util.Dom;
    var $E = YAHOO.util.Event;
    var _4 = YAHOO.util.KeyListener;
    var _5 = YAHOO.env.ua.ie;
    var _6 = (_5 == 6);
    var _7 = YAHOO.env.ua.gecko;
    var _8 = YAHOO.env.ua.webkit;
    var _9 = YAHOO.env.ua.opera;
    var _a = YAHOO.lang.isString;
    var _b = YAHOO.lang.trim;
    MT.app.Subscribe = {
        init: function () {
            var _c = $D.get("enter-address-form");
            var _d = $D.get("subscribe-other");
            var _e = $D.get("enter-address-mail");
            _e.focus();
            $E.on(_d, "click", function (e) {
                $E.stopEvent(e);
                _c.cityid.style.display = "none";
                _c.cityid.disable = true;
                _c.cityname.style.display = "inline";
                _d.style.display = "none";
                _c.cityname.focus();
                $D.get("enter-address-city-label").innerHTML = "\u8f93\u5165\u60a8\u7684\u57ce\u5e02\u540d\u79f0\uff1a";
            });
        }
    };
    MT.app.About = {
        job: function () {
            var _10 = $D.get("about-job-list");
            var _11 = $D.get("about-job-select");
            var _12 = $D.getElementsBy(function (el) {
                return el.getAttribute("scity");
            }, "li", _10);
            var _14 = window.location.hash;
            var _15 = [];
            for (var i = 0; i < _11.options.length; i++) {
                var opt = _11.options[i];
                if (opt.value !== "") {
                    _15.push(opt.value);
                }
            }
            if (_14 && _14.length > 1) {
                _14 = _14.substr(1);
            } else {
                _14 = null;
            }
            var _18 = $D.getElementsByClassName("job-title", "h3", _10, function (el) {
                $E.on(el, "click", function (e) {
                    var _1b = $D.getAncestorByTagName(this, "li");
                    var sid = _1b.getAttribute("sid");
                    toggle(_1b);
                    setHash(sid);
                });
            });
            $E.on(_11, "change", function (e) {
                $D.batch(_12, function (el) {
                    toggle(el, false);
                });
                showJobsByHash(this.value);
            });
            showJobsByHash(_14);
            selectByValue(_11, _14);

            function toggle(_1f, _20) {
                if (!_1f) {
                    return;
                }
                var _21 = $D.getElementsByClassName("job-title", "h3", _1f)[0];
                var _22 = $D.getElementsByClassName("job-desc", "div", _1f)[0];
                if (typeof _20 === "undefined") {
                    _20 = $D.getStyle(_22, "display") == "none";
                }
                if (_20) {
                    _22.style.display = "block";
                    $D.replaceClass(_21, "fold", "unfold");
                } else {
                    _22.style.display = "none";
                    $D.replaceClass(_21, "unfold", "fold");
                }
            }
            function showJobsByHash(_23) {
                var _24 = null;
                if (_23 && _23.length) {
                    $D.batch(_12, function (el) {
                        var _26 = el.getAttribute("scity");
                        var sid = el.getAttribute("sid");
                        if (MT.util.inArray(_23, _15)) {
                            var _28 = _26.split("|");
                            if (MT.util.inArray(_23, _28)) {
                                el.style.display = "block";
                            } else {
                                el.style.display = "none";
                            }
                        } else {
                            if (sid) {
                                if (_23 == sid) {
                                    toggle(el, true);
                                    if (!_24) {
                                        _24 = el;
                                    }
                                }
                                el.style.display = "block";
                            }
                        }
                    });
                } else {
                    $D.batch(_12, function (el) {
                        el.style.display = "block";
                    });
                }
                _10.style.display = "block";
                setHash(_23);
                if (_24) {
                    var _2a = $D.getXY(_24);
                    window.setTimeout(function () {
                        window.scrollTo(0, _2a[1]);
                    }, 100);
                }
            }
            function setHash(_2b) {
                if (_2b) {
                    window.location.hash = "#" + _2b;
                }
            }
            function selectByValue(_2c, _2d) {
                if (!_2c) {
                    return;
                }
                for (var i = 0; i < _2c.options.length; i++) {
                    var _2f = _2c.options[i];
                    if (_2f.value == _2d) {
                        _2f.selected = true;
                        break;
                    }
                }
            }
        }
    };
    MT.app.Account = {
        _noticeDialog: null,
        getNoticeDialog: function () {
            if (!this._noticeDialog) {
                var _30 = {
                    width: "344px",
                    fixedcenter: true,
                    close: false,
                    draggable: false,
                    zindex: 7,
                    modal: true,
                    visible: false
                };
                this._noticeDialog = new YAHOO.widget.Panel("order-pay-dialog", _30);
            }
            return this._noticeDialog;
        },
        charge: function () {
            var _31 = $D.get("account-charge-form");
            if (!_31) {
                return;
            }
            var _32 = _31.amount;
            var _33 = $D.get("account-charge-tip");
            var _34 = /^[\d\.]+$/;
            var _35 = window.location.hash;
            var _36 = 500;
            var tip = "\u63d0\u793a\uff1a\u5145\u503c\u91d1\u989d\u8d85\u51fa\u90e8\u5206\u94f6\u884c\u5355\u7b14\u652f\u4ed8\u989d\u5ea6\uff0c\u5efa\u8bae\u6bcf\u6b21\u5145\u503c\u5c0f\u4e8e499\u5143";//提示：充值金额超出部分银行单笔支付额度，建议每次充值小于499元
            if (_35) {
                _35 = _35.replace("#", "");
                var _38 = parseFloat(_35);
                if (_38) {
                    _32.value = _38;
                }
            }
            _32.focus();
            $E.on(_32, "keyup", function (e) {
                if (_32.value >= _36) {
                    _33.innerHTML = tip;
                    _33.style.visibility = "visible";
                } else {
                    _33.style.visibility = "hidden";
                }
            });
            $E.on(_31, "submit", function (e) {
                $E.stopEvent(e);
                if (!_32.value) {
                    alert("\u5145\u503c\u91d1\u989d\u4e0d\u80fd\u4e3a\u7a7a");
                    _32.focus();
                } else {
                    if (!_34.test(_32.value) || _32.value < 0.01) {
                        alert("\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u5145\u503c\u91d1\u989d");
                        _32.focus();
                    } else {
                        this.submit();
                    }
                }
            });
        },
        chargePay: function () {
            var _3b = $D.get("order-pay-form");
            var _3c = $D.get("order-pay-button");
            var _3d = this;
            $E.on(_3c, "click", function (e) {
                showNotice();
            });

            function showNotice() {
                var _3f = _3d.getNoticeDialog();
                var _40 = $D.get("total-money");
                var _41 = "/account/charge";
                if (_40) {
                    var _42 = parseFloat(_40.innerHTML);
                    if (_42) {
                        _41 += "#" + _42;
                    }
                }
                var _43 = ["<div class=\"order-pay-dialog-c\">"];
                _43.push("<h3><span id=\"order-pay-dialog-close\" class=\"close\">\u5173\u95ed</span></h3>");
                _43.push("<p class=\"info\">\u8bf7\u60a8\u5728\u65b0\u6253\u5f00\u7684\u9875\u9762\u4e0a<br />\u5b8c\u6210\u4ed8\u6b3e\u3002</p>");
                _43.push("<p class=\"notice\">\u4ed8\u6b3e\u5b8c\u6210\u524d\u8bf7\u4e0d\u8981\u5173\u95ed\u6b64\u7a97\u53e3\u3002<br />\u5b8c\u6210\u4ed8\u6b3e\u540e\u8bf7\u6839\u636e\u60a8\u7684\u60c5\u51b5\u70b9\u51fb\u4e0b\u9762\u7684\u6309\u94ae\uff1a</p>");
                _43.push("<p class=\"act\"><input id=\"order-pay-dialog-succ\" type=\"submit\" class=\"formbutton\" value=\"\u5df2\u5b8c\u6210\u4ed8\u6b3e\" /> <input id=\"order-pay-dialog-fail\" type=\"submit\" class=\"formbutton\" value=\"\u4ed8\u6b3e\u9047\u5230\u95ee\u9898\" /></p>");
                _43.push("<p class=\"retry\"><a href=\"" + _41 + "\">&raquo; \u8fd4\u56de\u9009\u62e9\u5176\u4ed6\u652f\u4ed8\u65b9\u5f0f</p>");
                _43.push("</div>");
                _3f.setBody(_43.join(""));
                _3f.render(document.body);
                _3f.show();
                $E.on("order-pay-dialog-succ", "click", function (e) {
                    _3f.hide();
                    window.location.href = "/account/credit";
                });
                $E.on("order-pay-dialog-fail", "click", function (e) {
                    _3f.hide();
                    window.location.href = "/account/credit";
                });
                $E.on("order-pay-dialog-close", "click", function (e) {
                    _3f.hide();
                    window.location.href = "/account/credit";
                });
            }
        },
        cardCharge: function () {
            var _47 = $D.get("credit-card-form");
            var _48 = _47.cardcode;
            var _49 = _47.cardcodefake;
            var _4a = $D.get("credit-card-link");
            var _4b = $D.get("credit-card-enter");
            var _4c = $D.get("credit-card-notice");
            var _4d = $D.get("credit-card-captcha");
            var _4e = $D.get("credit-card-captcha-img");
            var _4f = $D.get("credit-card-submit");
            var _50 = $D.get("credit-card-cancel");
            var _51 = false;
            $E.on(_4a, "click", function (e) {
                if (_4b.style.display == "none") {
                    _4b.style.display = "block";
                    _49.focus();
                } else {
                    _4b.style.display = "none";
                }
            });
            $E.on(_47, "submit", function (e) {
                $E.stopEvent(e);
                var _54 = $D.hasClass(_47, "isvalid");
                if (_54) {
                    window.setTimeout(function () {
                        _47.submit();
                    }, 10);
                } else {
                    var _55 = _49.value;
                    if (_55 === "") {
                        _49.focus();
                    } else {
                        var _56 = "cardcode=" + _55;
                        var _57 = _47.captcha.value;
                        if (isNeedCaptcha() && _57 === "") {
                            _47.captcha.focus();
                            return;
                        } else {
                            _56 += "&captcha=" + _57;
                        }
                        var _58 = $C.asyncRequest("POST", "/card/chargecheck", {
                            success: function (o) {
                                handleResponse(o, _55);
                            },
                            failure: function (o) {}
                        }, _56);
                    }
                }
            });
            $E.on(_50, "click", function (e) {
                $D.removeClass(_47, "isvalid");
                _4c.style.display = "none";
                _49.disabled = false;
                _48.value = "";
                _4f.value = "\u786e\u5b9a";
                _50.style.display = "none";
                _49.focus();
                if (isNeedCaptcha()) {
                    $D.addClass(_4d, "show");
                }
                refreshCaptcha();
            });

            function handleResponse(o, _5d) {
                var res = MT.util.getEvalRes(o);
                if (res.status) {
                    $D.addClass(_47, "isvalid");
                    _49.disabled = true;
                    _48.value = _5d;
                    _4f.value = "\u5145\u503c";
                    _4c.innerHTML = res.msg;
                    _4c.style.display = "block";
                    _50.style.display = "";
                    _47.token.value = res.data.token;
                    _51 = res.data.needCaptcha;
                    if (!_51) {
                        $D.removeClass(_4d, "show");
                    }
                } else {
                    _4c.innerHTML = res.msg;
                    _4c.style.display = "block";
                    _49.focus();
                    if (res.data && res.data.needCaptcha) {
                        $D.addClass(_4d, "show");
                        _51 = true;
                        refreshCaptcha();
                    }
                }
            }
            function isNeedCaptcha() {
                return _51 || $D.hasClass(_4d, "show");
            }
            function refreshCaptcha() {
                if (isNeedCaptcha()) {
                    _4e.src = _4e.getAttribute("surl") + "?" + Math.random();
                }
            }
        }
    };
    MT.app.Vote = {
        init: function () {
            var _5f = $D.getElementsByClassName("choice", "input", "content", function (el) {
                $E.on(el, "click", function (e) {
                    var el = $D.get(this.getAttribute("sid"));
                    if (el) {
                        if (this.type == "checkbox") {
                            if (this.checked) {
                                el.disabled = false;
                                $D.removeClass(el, "disabled");
                                el.focus();
                            } else {
                                el.disabled = true;
                                $D.addClass(el, "disabled");
                            }
                        } else {
                            if (this.type == "radio") {
                                if (this.value == el.getAttribute("cid")) {
                                    el.disabled = false;
                                    $D.removeClass(el, "disabled");
                                    el.focus();
                                } else {
                                    el.disabled = true;
                                    $D.addClass(el, "disabled");
                                }
                            }
                        }
                    }
                });
            });
            var _63 = $D.get("label-12-83");
            var _64 = $D.get("label-12-84");
            var _65 = $D.get("vote-list-13");
            $E.on(_63, "click", function (e) {
                _65.style.display = "block";
            });
            $E.on(_64, "click", function (e) {
                _65.style.display = "none";
            });
        }
    };
    MT.app.Finder = {
        _createLoading: function () {
            var _68 = {
                width: "265px",
                fixedcenter: true,
                close: false,
                draggable: false,
                zindex: 7,
                modal: true,
                visible: false
            };
            var _69 = new YAHOO.widget.Panel("account-finder-wait", _68);
            _69.setHeader("<h3>\u6b63\u5728\u9a8c\u8bc1\uff0c\u8bf7\u7a0d\u5019...</h3>");
            _69.setBody("<div><p><img src=\"/static/loading-progress.gif\" width=\"208\" height=\"13\" alt=\"...\" /></p><p style=\"*margin-top:3px;\">\u6b64\u8fc7\u7a0b\u53ef\u80fd\u9700\u8981\u51e0\u5206\u949f\uff0c\u8bf7\u8010\u5fc3\u7b49\u5f85</p></div>");
            _69.render(document.body);
            $D.addClass(document.body, "yui-skin-sam");
            return _69;
        },
        submitProcess: function () {
            var _6a = $D.get("finder-form");
            MT.util.loadImage("/static/sprite.png");
            MT.util.loadImage("/static/loading-progress.gif");
            $E.on(_6a, "submit", function (e) {
                $E.stopEvent(e);
                var _6c = _6a["finder-login"];
                var _6d = _6a["finder-password"];
                if (!_6c.value) {
                    alert("MSN\u8d26\u53f7\u4e0d\u80fd\u4e3a\u7a7a");
                    _6c.focus();
                } else {
                    if (!_6d.value) {
                        alert("\u5bc6\u7801\u4e0d\u80fd\u4e3a\u7a7a");
                        _6d.focus();
                    } else {
                        var _6e = MT.app.Finder._createLoading();
                        _6e.show();
                        window.setTimeout(function () {
                            _6a.submit();
                        }, 300);
                    }
                }
            });
        },
        handleTable: function () {
            var _6f = $D.get("finder-contacts-form");
            var _70 = $D.get("finder-contacts-table");
            var _71 = $D.get("finder-selectall");
            var _72 = _5 ? "propertychange" : "change";
            var trs = _70.getElementsByTagName("tr");
            var _74 = $D.getElementsBy(function (el) {
                return el.type == "checkbox";
            }, "input", _70);
            $E.on(_70, "click", function (e) {
                var t = $E.getTarget(e),
                    nn = t.nodeName.toLowerCase(),
                    p;
                if (nn == "input") {
                    return;
                }
                if (nn != "tr") {
                    p = $D.getAncestorByTagName(t, "tr");
                } else {
                    p = t;
                }
                var _7a = p.getElementsByTagName("input")[0];
                _7a.checked = !_7a.checked;
            });
            $E.on(_71, _72, function (e) {
                var _7c = false;
                if (_71.checked) {
                    _7c = true;
                }
                $D.batch(_74, function (_7d) {
                    _7d.checked = _7c;
                });
            });
            $E.on(_6f, "submit", function (e) {
                $E.stopEvent(e);
                var _7f = false;
                for (var i = 0, len = _74.length; i < len; i++) {
                    if (_74[i].checked) {
                        _7f = true;
                        break;
                    }
                }
                if (_7f) {
                    this.submit();
                } else {
                    alert("\u8bf7\u60a8\u81f3\u5c11\u9009\u62e9\u4e00\u4e2a\u597d\u53cb");
                }
            });
        }
    };
    MT.app.Help = {
        toggleList: function () {
            var _82 = $D.get("content");
            var _83 = _82.getElementsByTagName("dt");
            var _84 = _82.getElementsByTagName("dd");
            var _85 = "fold";
            var _86 = "unfold";
            $D.batch(_83, function (el) {
                if (!$D.hasClass(el, _86)) {
                    $D.addClass(el, _85);
                }
                $E.on(el, "click", function (e) {
                    var _89 = $D.hasClass(this, "fold");
                    var _8a = $D.getNextSibling(this);
                    if (_89) {
                        $D.batch(_83, function (i) {
                            $D.replaceClass(i, _86, _85);
                        });
                        $D.batch(_84, function (i) {
                            i.style.display = "none";
                        });
                        $D.replaceClass(this, _85, _86);
                        _8a.style.display = "block";
                    } else {
                        $D.replaceClass(this, _86, _85);
                        _8a.style.display = "none";
                    }
                });
                $E.on(el, "mouseover", function (e) {
                    $D.addClass(this, "hover");
                });
                $E.on(el, "mouseout", function (e) {
                    $D.removeClass(this, "hover");
                });
            });
        }
    };
    MT.app.Coupon = {
        initAction: function () {
            var _8f = $D.get("coupons-table");
            var _90 = "/static/indicator-large.gif";
            MT.util.loadImage(_90);
            $D.getElementsByClassName("coupon-form", "form", _8f, function (el) {
                initForm(el);
            });
            $E.on(_8f, "click", function (e) {
                var _93 = $E.getTarget(e);
                var _94 = _93.nodeName.toLowerCase();
                if (_94 !== "a") {
                    return;
                }
                if ($D.hasClass(_93, "coupon-view")) {
                    view(_93);
                } else {
                    if ($D.hasClass(_93, "coupon-send")) {
                        send(_93);
                    }
                }
            });

            function view(el) {
                var _96 = getParent(el);
                var _97 = $D.getElementsByClassName("view", "div", _96)[0];
                var _98 = $D.getElementsByClassName("sendsms", "div", _96)[0];
                _98.style.display = "none";
                if ($D.getStyle(_97, "display") === "none") {
                    _97.style.display = "block";
                } else {
                    _97.style.display = "none";
                }
            }
            function send(el) {
                var _9a = getParent(el);
                var _9b = $D.getElementsByClassName("view", "div", _9a)[0];
                var _9c = $D.getElementsByClassName("sendsms", "div", _9a)[0];
                var _9d = _9a.getElementsByTagName("form")[0];
                _9b.style.display = "none";
                if ($D.getStyle(_9c, "display") == "none") {
                    _9c.style.display = "block";
                    try {
                        _9d.mobile.focus();
                    } catch (e) {}
                } else {
                    _9c.style.display = "none";
                }
            }
            function getParent(el) {
                return $D.getAncestorByClassName(el, "coupon-box");
            }
            function initForm(_9f) {
                var _a0 = _9f.mobile;
                var _a1 = _9f.commit;
                var _a2 = $D.getElementsByClassName("enter", "p", _9f)[0];
                var _a3 = $D.getElementsByClassName("msg", "p", _9f)[0];
                var _a4 = getParent(_9f);
                var _a5 = _a4.getAttribute("smobile");
		var _a6 = _a4.getAttribute("scardid");
                function check(e) {
                    if (/^[0-9]{11}$/.test(_a0.value)) {
                        _a1.disabled = false;
                        $D.removeClass(_a1, "disabled");
                    } else {
                        _a1.disabled = true;
                        $D.addClass(_a1, "disabled");
                    }
                }
                $E.on(_a0, "keyup", check);
                $E.on(_a0, "change", check);
                $E.on(_9f, "submit", function (e) {
                    $E.stopEvent(e);
                    $C.setForm(_9f);
                    var _a8 = $C.asyncRequest("GET", "/coupons.php?act=send_sms"+"&card_id="+_a6, {
                        success: function (o) {
                            var ret = MT.util.getEvalRes(o, "\u672a\u77e5\u9519\u8bef");//未知错误
                            _a2.style.display = ret.status == 1 ? "none" : "block";
                            _a3.style.display = "block";
                            var _ab = ret.status == 1 ? "sendsms-success" : "sendsms-failure";
                            _a3.innerHTML = "<span class=\"" + _ab + "\">" + ret.msg + "</span>";
                        },
                        failure: function (o) {
                            _a2.style.display = "block";
                            _a3.style.display = "block";
                            _a3.innerHTML = "<span class=\"sendsms-failure\">\u8fde\u63a5\u8d85\u65f6</span>";//连接超时
                        },
                        timeout: 18000
                    });
                    if ($C.isCallInProgress(_a8)) {
                        _a2.style.display = "none";
                        _a3.innerHTML = "<img src=\"" + _90 + "\" />";
                    }
                });
                return _9f;
            }
        }
    };
})();