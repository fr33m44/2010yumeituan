MT.namespace("app.Signup");
MT.app.Signup = (function () {
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
    var _b = MT.util.getLength;
    var _c = MT.util.toHalf;
    var _d = {
        username: /^[\.\w\u4e00-\u9fa5\uF900-\uFA2D]{2,16}$/ig,
        password: /^[\x21-\x7e]{6,32}$/ig,
        email: /^[\w\.\-\+]+@([\w\-]+\.)+[a-z]{2,4}$/ig
    };
    var _e = true;

    function checkReg(_f, reg) {
        if (!_f) {
            return false;
        }
        reg.lastIndex = 0;
        return reg.test(_f);
    }
    function setHint(el, _12, _13) {
        _12 = _12 ? _12 : "";
        var _14 = null;
        if (_e) {
            _13 = _13 ? _13 : "&nbsp;";
            if (_12 === "right") {
                $D.removeClass(el, "highlight");
                _13 = "&nbsp;";
            } else {
                if (_12 === "wrong") {
                    $D.addClass(el, "highlight");
                }
            }
            _14 = $D.get(el.name + "-hint");
            if (_14) {
                _14.className = _12;
                _14.innerHTML = _13;
            }
        } else {
            var _15 = getParent(el);
            var els = $D.getElementsByClassName("hint", "span", _15);
            if (!els.length) {
                return;
            }
            _14 = els[0];
            if (_12 === "right") {
                $D.removeClass(el, "highlight");
                _14.className = "hint";
                _13 = _14.getAttribute("stext");
                _14.innerHTML = _13 ? _13 : "&nbsp;";
            } else {
                if (_12 === "wrong") {
                    $D.addClass(el, "highlight");
                    _14.className = "hint bad";
                    _14.innerHTML = _13 ? _13 : "&nbsp;";
                } else {
                    if (_12 === "loading") {
                        _14.className = "hint";
                        _13 = _14.getAttribute("stext");
                        _14.innerHTML = _13 ? _13 : "&nbsp;";
                    } else {
                        if (!_12 && el.value === "") {
                            _14.className = "hint";
                            _13 = _14.getAttribute("stext");
                            _14.innerHTML = _13 ? _13 : "&nbsp;";
                        }
                    }
                }
            }
        }
    }
    function getParent(el) {
        return $D.getAncestorByClassName(el, "field");
    }
    function getFormField(el) {
        var _19 = getParent(el);
        var _1a = $D.getElementsByClassName("f-text", "input", _19);
        return _1a.length ? _1a[0] : null;
    }
    return {
        init: function () {
            var _1b = $D.get("signup-user-form");
            if (!_1b) {
                return;
            }
            var _1c = _1b.username;
            var _1d = _1b.password;
            var _1e = _1b.password2;
            var _1f = _1b.email;
            var _20 = [];
            var _21;
            var _22 = _9 ? "input" : "keyup";
            var _23 = this;
            if (!_1f.value) {
                _1f.focus();
            } else {
                _1c.focus();
            }
            _e = _1b.getAttribute("sfrom") === "home";
            MT.app.Core.autoCompleteEmail(_1f, "signup-email-auto");
            $D.getElementsByClassName("hint", "span", _1b, function (el) {
                el.setAttribute("stext", el.innerHTML);
            });
            $E.on(_1b, "submit", function (e) {
                $E.stopEvent(e);
                if (_1f.value === "") {
                    setHint(_1f, "wrong", "\u8bf7\u8f93\u5165Email");//请输入Email
                    _1f.focus();
                    return;
                } else {
                    if (_1c.value === "") {
                        setHint(_1c, "wrong", "\u8bf7\u8f93\u5165\u7528\u6237\u540d");//请输入用户名
                        _1c.focus();
                        return;
                    } else {
                        if (_1d.value === "") {
                            setHint(_1d, "wrong", "\u8bf7\u8f93\u5165\u5bc6\u7801");//请输入密码
                            _1d.focus();
                            return;
                        } else {
                            if (_1e.value === "") {
                                setHint(_1e, "wrong", "\u8bf7\u8f93\u5165\u786e\u8ba4\u5bc6\u7801");//请输入确认密码
                                _1e.focus();
                                return;
                            }
                        }
                    }
                }
                var _26 = $D.getElementsBy(function (el) {
                    return $D.hasClass(el, "wrong") || $D.hasClass(el, "bad");
                }, "span", _1b);
                if (_26.length) {
                    var el = _26[0];
                    var _29 = getFormField(el);
                    if (_29) {
                        try {
                            _29.focus();
                        } catch (e) {}
                    }
                } else {
                    _1b.submit();
                }
            });
            var _2a = false;
            _20["check-username"] = window.setInterval(function () {
                if (_2a) {
                    return;
                }
                var _2b = _c(_1c.value);
                if (_21 == _2b) {
                    return;
                } else {
                    _21 = _2b;
                }
                if (_b(_2b) < 2) {
                    return;
                }
                _2a = true;
                var _2c = "username=" + encodeURIComponent(_2b);
                var _2d = $C.asyncRequest("post", "addon.php?act=account_check", {
                    success: function (o) {
                        var res = MT.util.getEvalRes(o);
                        if (res.status == 1) {
                            setHint(_1c, "right", res.msg);
                        } else {
                            setHint(_1c, "wrong", res.msg);
                        }
                        _2a = false;
                    },
                    failure: function (o) {}
                }, _2c);
                if ($C.isCallInProgress(_2d)) {
                    setHint(_1c, "loading");
                }
            }, 1000);
            $E.on(_1c, _22, function (e) {
                var _32 = _1c.value;
                var _33 = _c(_32);
                if (_33 != _32) {
                    _1c.value = _33;
                    _32 = _33;
                }
                var len = _b(_32);
                if (len < 4) {
                    setHint(_1c, "");
                }
            });
            $E.on(_1c, "blur", function (e) {
                var _36 = _1c.value;
                var len = _b(_36);
                var _38 = checkReg(_36, _d["username"]);
                if (_36 === "") {
                    setHint(_1c, "wrong", "\u8bf7\u8f93\u5165\u7528\u6237\u540d");//请输入用户名
                } else {
                    if (len < 2) {
                        setHint(_1c, "wrong", "\u7528\u6237\u540d\u592a\u77ed\uff0c\u6700\u5c11 1 \u4e2a\u6c49\u5b57\u6216 2 \u4e2a\u5b57\u7b26");//用户名太短，最少 1 个汉字或 2 个字符
                    } else {
                        if (len > 16) {
                            setHint(_1c, "wrong", "\u7528\u6237\u540d\u592a\u957f\uff0c\u6700\u591a 16 \u4e2a\u5b57\u7b26\u6216 8 \u4e2a\u6c49\u5b57");//用户名太长，最多 16 个字符或 8 个汉字
                        } else {
                            if (/^[\.\_-]/.test(_36)) {
                                setHint(_1c, "wrong", "\u8bf7\u4ee5\u4e2d\u6587\u6216\u82f1\u6587\u5b57\u6bcd\u5f00\u5934");//请以中文或英文字母开头
                            }
                        }
                    }
                }
            });
            $E.on(_1f, "blur", function (e) {
                if (_1f.value === "") {
                    setHint(_1f, "wrong", "\u8bf7\u8f93\u5165Email");
                    return;
                }
                var _3a = _1f.value;
                var _3b = checkReg(_3a, _d["email"]);
                setHint(_1f, "");
                if (_3b) {
                    var _3c = "email=" + encodeURIComponent(_3a);
                    var _3d = $C.asyncRequest("post", "addon.php?act=account_check", {
                        success: function (o) {
                            var res = MT.util.getEvalRes(o);
                            if (res.status == 1) {
                                setHint(_1f, "right", res.msg);
                            } else {
                                setHint(_1f, "wrong", res.msg);
                            }
                        },
                        failure: function (o) {}
                    }, _3c);
                    if ($C.isCallInProgress(_3d)) {
                        setHint(_1f, "loading");
                    }
                } else {
                    setHint(_1f, "wrong", "Email\u683c\u5f0f\u6709\u8bef\uff0c\u8bf7\u91cd\u65b0\u8f93\u5165");
                }
            });
            $E.on(_1f, "focus", function (e) {
                setHint(_1f, "");
            });
            $E.on(_1f, _22, function (e) {
                var _43 = _1f.value;
                var _44 = _c(_43);
                if (_44 != _43) {
                    _1f.value = _44;
                }
            });
            $E.on(_1d, "keypress", function (e) {
                if (MT.util.checkCapsLock(e)) {
                    setHint(_1d, "warning", "\u63d0\u9192\uff1a\u5927\u5199\u9501\u5b9a\u952e\u5728\u5f00\u542f\u72b6\u6001");
                } else {
                    if (_1d.value !== "") {
                        setHint(_1d, "");
                    }
                }
            });
            $E.on(_1e, "keypress", function (e) {
                if (MT.util.checkCapsLock(e)) {
                    setHint(_1e, "warning", "\u63d0\u9192\uff1a\u5927\u5199\u9501\u5b9a\u952e\u5728\u5f00\u542f\u72b6\u6001");
                } else {
                    setHint(_1e, "");
                }
            });
            $E.on(_1d, "blur", function (e) {
                var _48 = this.value;
                if (_48 === "") {
                    setHint(_1d, "wrong", "\u8bf7\u8f93\u5165\u5bc6\u7801");
                    return;
                }
                var _49 = checkReg(_48, _d["password"]);
                if (_49) {
                    setHint(_1d, "right", "\u975e\u5e38\u597d");
                } else {
                    setHint(_1d, "wrong", "\u5bc6\u7801\u592a\u77ed\uff0c\u6700\u5c11 6 \u4e2a\u5b57\u7b26");
                }
            });
            $E.on(_1e, "blur", function (e) {
                var _4b = this.value;
                var _4c = _1d.value;
                if (_4b === "") {
                    setHint(_1e, "wrong", "\u8bf7\u8f93\u5165\u786e\u8ba4\u5bc6\u7801");
                    return;
                }
                var _4d = checkReg(_4b, _d["password"]);
                if (_4b !== _4c) {
                    setHint(_1e, "wrong", "\u5bc6\u7801\u4e0d\u4e00\u81f4\uff0c\u8bf7\u91cd\u65b0\u8f93\u5165");
                } else {
                    if (_4d) {
                        setHint(_1e, "right", "\u975e\u5e38\u597d");
                    } else {
                        setHint(_1e, "wrong", "\u5bc6\u7801\u592a\u77ed\uff0c\u6700\u5c11 6 \u4e2a\u5b57\u7b26");
                    }
                }
            });
        }
    };
})();