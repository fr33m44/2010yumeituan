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
    MT.app.Deal = {
        refresh: function (_c, _d) {
            var _e = 60;
            var _f = 5;
            var max = 300;
            var _11 = $D.get("deal-timeleft");
            var _12 = $D.hasClass(_11, "deal-on");
            var _13 = $D.get("deal-status");
            var _14 = $D.get("counter");
            var _15 = $D.get("counter-hour");
            var _16 = $D.get("counter-minute");
            var _17 = $D.get("counter-second");
            var _18 = null;
            var _19 = null;
            var _1a = 86400;
            if (_12) {
                var _1b = _11.getAttribute("diff");
                countTime(_1b);
            } else {
                _e = max;
                if (!_d) {
                    return;
                }
            }
            _18 = window.setInterval(check, _e * 1000);

            function check() {
                $C.asyncRequest("GET", "/deal/status/" + _c, {
                    success: function (o) {
                        handleResponse(o);
                    },
                    failure: function (o) {}
                });
            }
            function handleResponse(o) {
                var res = MT.util.getEvalRes(o);
                if (res.status) {
                    if (res.data["open"]) {
                        _13.innerHTML = res.data["html"];
                    } else {
                        if (_12) {
                            //window.location.reload();
                            return;
                        } else {
                            var _20 = res.data["timeleft"];
                            if (_1a > _20) {
                                _1a = _20;
                                window.clearTimeout(_19);
                                _19 = window.setTimeout(function () {
                                    //window.location.reload();
                                }, _20 * 1000);
                            }
                        }
                    }
                }
                window.clearInterval(_18);
                if (_e < max) {
                    _e += _f;
                }
                _18 = window.setInterval(check, _e * 1000);
            }
            function countTime() {
                if (_1b < 1) {
                    //window.location.reload();
                    return;
                }
                var day = parseInt(_1b / (60 * 60 * 24), 10);
                var _22 = parseInt((_1b / (60 * 60)) % 24, 10);
                var _23 = parseInt((_1b / 60) % 60, 10);
                var _24 = parseInt(_1b % 60, 10);
                var _25 = [];
                if (day > 0) {
                    _25.push("<li><span>" + day + "</span>\u5929</li>");//天
                    _25.push("<li><span>" + _22 + "</span>\u5c0f\u65f6</li>");//小时
                    _25.push("<li><span>" + _23 + "</span>\u5206\u949f</li>");//小时
                } else {
                    _25.push("<li><span>" + _22 + "</span>\u5c0f\u65f6</li>");//小时
                    _25.push("<li><span>" + _23 + "</span>\u5206\u949f</li>");//分钟
                    _25.push("<li><span>" + _24 + "</span>\u79d2</li>");//秒
                }
                _14.innerHTML = _25.join("");
                _1b--;
                window.setTimeout(countTime, 1000);
            }
        },
        toggle: function (_26, _27) {
            _26 = $D.get(_26);
            _27 = $D.get(_27);
            $E.on(_26, "click", function (e) {
                _27.style.display = $D.getStyle(_27, "display") == "none" ? "block" : "none";
            });
        },
        toggleOtherShare: function (_29) {
            var _2a = $D.get("deal-share-im");
            var _2b = $D.get("deal-share-im-c");
            var _2c = $D.get("sidebar");
            $E.on(_2a, "click", function (e) {
                var _2e = $D.getStyle(_2b, "display") == "none";
                _2b.style.display = _2e ? "block" : "none";
                if (_29) {
                    _2c.style.marginTop = _2e ? "70px" : "0px";
                }
            });
        },
        mailto: function (el) {
            el = $D.get(el);
            var _30 = el.getAttribute("href");
            var _31 = _30.match(/\?subject=(.+)&body/)[1];
            var _32 = _30.match(/&body=(.+)/)[1];
            $E.on(el, "click", function (e) {
                try {
                    var _34 = new ActiveXObject("Outlook.Application");
                    var _35 = _34.getNameSpace("MAPI");
                    var _36 = _34.CreateItem(0);
                    $E.stopEvent(e);
                    _36.Subject = _31;
                    _36.To = "";
                    _36.HTMLBody = _32;
                    _36.Display(0);
                } catch (e) {}
            });
        },
        check: function () {
            if (_5) {
                fixLableImage();
            }
            function fixLableImage() {
                $E.on(["check-alipay-l", "check-chinabank-l"], "click", function (e) {
                    $D.get(this.parentNode.htmlFor).checked = true;
                });
            }
        },
        removeGuide: function () {
            $E.on("sysmsg-guide-close", "click", function (e) {
                var _39 = this;
                $E.stopEvent(e);
                $C.asyncRequest("POST", "addon.php?act=remove_tips", {
                    success: function (o) {
                        var res = MT.util.getEvalRes(o);
                        if (res.status) {
                            $D.removeClass(document.body, "bg-newbie");
                            _39.parentNode.style.display = "none";
                        } else {
                            alert(res.msg);
                        }
                    },
                    failure: function (o) {}
                });
            });
        },
        addConsult: function () {
            var _3d = $D.get("consult-add-succ");
            var _3e = $D.get("consult-add-more");
            var _3f = $D.get("consult-add-form");
	    var _40 = $D.get("consult-add-ask");
            $E.on(_40, "click", function (e) {
                _3f.style.display = "block";
                this.style.display = "none";
            });
            $E.on(_3f, "submit", function (e) {
                $E.stopEvent(e);
                $C.setForm(this);
                if (!this.question.value) {
                    return alert("\u95ee\u9898\u4e0d\u80fd\u4e3a\u7a7a!");
                }
                var _41 = $C.asyncRequest("POST", _3f.action, {
                    success: handleResponse,
                    failure: function (o) {}
                });
                return true;
            });
            $E.on(_3e, "click", function (e) {
                $E.stopEvent(e);
                var _44 = _3f.question;
                _44.value = "";
                _3f.style.display = "block";
                _3d.style.display = "none";
                window.setTimeout(function () {
                    _44.focus();
                }, 0);
            });

            function handleResponse(o) {
                var res = MT.util.getEvalRes(o);
                if (res.status) {
                    _3f.style.display = "none";
                    _3d.style.display = "block";
                    MT.util.Effect.yellowFadeIn(_3d);
                } else {
                    alert(res.msg);
                }
            }
        }
    };
    MT.app.Deal.buy = function (_47, _48, _49) {
        var _4a = $D.get("deal-buy-form");
        var _4b = $D.get("deal-buy-quantity-input");
        var _4c = $D.get("deal-buy-mobile-input");
        var _4d = $D.get("deal-buy-mobile-tip");
        var _4e = $D.get("deal-buy-type-select-t");
        var _4f = $D.get("deal-buy-type-select-b");
        var _50 = $D.get("deal-buy-quantity-login");
        var _51 = $D.get("deal-buy-quantity-signup");
        var _52 = /^\d{11}$/;
        var _53 = "\u8bf7\u8f93\u5165\u6b63\u786e\u768411\u4f4d\u624b\u673a\u53f7\u7801";//请输入正确的11位手机号码
        var _54 = "\u8bf7\u9009\u62e9\u5546\u54c1\u7c7b\u578b";
        var _55 = _5 ? "propertychange" : "change";
        var _56 = "type-select-dialog";
        var _57 = null;

        function showError(_58) {
            MT.widget.showMessage("error", _58);
        }
        function setValue(el, _5a) {
            if (el) {
                el.value = _5a;
            }
        }
        function canBuy(e, _5c) {
            var _5d = parseInt(_4b.value, 10);
            var _5e = /^[0-9]+$/;
            var str;
            if (!_4b.value) {
                if (_5c) {
                    showError("\u8d2d\u4e70\u6570\u91cf\u4e0d\u80fd\u4e3a\u7a7a");
                }
                return false;
            } else {
                if (!_5e.test(_4b.value)) {
                    showError("\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u8d2d\u4e70\u6570\u91cf");
                    return false;
                }
            }
            if (_47) {
                if (_5d > _47) {
                    showError("\u672c\u5355\u53ea\u5269 " + _47 + " \u4ef6\u4e86");
                    return false;
                }
            }
            if (_48) {
                if (_5d > _48) {
                    str = "\u62b1\u6b49\uff0c\u6570\u91cf\u6709\u9650\uff0c\u60a8\u6700\u591a\u53ea\u80fd\u8d2d\u4e70 " + _48 + " \u4ef6";
                    showError(str);
                    return false;
                }
            }
            if (_5d < _49) {
                str = "\u60a8\u6700\u5c11\u9700\u8981\u8d2d\u4e70 " + _49 + " \u4ef6";
                showError(str);
                return false;
            }
            return true;
        }
        function fixNumber(_60) {
            var str = _60 + "";
            var _62 = /\d+\.\d+/;
            if (_62.test(str)) {
                return Number(_60).toFixed(2);
            } else {
                return _60;
            }
        }
        function check(e) {
            if (!canBuy(e)) {
                return false;
            }
            MT.widget.removeMessage();
            var _64 = parseInt(_4b.value, 10);
            var _65 = $D.get("deal-buy-price").innerHTML;
            var _66 = _64 * _65;
            var _67 = $D.get("deal-buy-delivery-fee");
            var _68 = $D.get("delivery-discount-row");
            var _69 = $D.get("deal-buy-delivery-discount");
            var _6a = parseFloat($D.get("cardcode-row-t").innerHTML);
            var _6b = _66 - _6a;
            if (_67) {
                _6b += parseFloat(_67.innerHTML, 10);
            }
            if (_68) {
                var _6c = _68.getAttribute("sdata");
                if (_6c > 0 && _64 >= _6c) {
                    _6b -= parseFloat(_69.innerHTML, 10);
                    _68.style.display = "";
                } else {
                    _68.style.display = "none";
                }
            }
            $D.get("deal-buy-total").innerHTML = fixNumber(_66);
            $D.get("deal-buy-total-t").innerHTML = fixNumber(_6b);
            setValue(_50, _64);
            setValue(_51, _64);
            return true;
        }
        initEvent();

        function initEvent() {
            $E.on(_4b, "keyup", function (e) {
                if (_4b.value === "") {
                    return;
                }
                check(e);
            });
            $E.on(_4b, "click", function (e) {
                if (_4e) {
                    selectType();
                }
            });
            $E.on(_4b, "blur", function (e) {
                if (canBuy(e)) {
                    MT.widget.removeMessage();
                }
            });
            $E.on(_4c, "keyup", function (e) {
                if (_52.test(this.value)) {
                    MT.widget.removeMessage();
                }
            });
            $E.on(_4c, "blur", function (e) {
                if (_52.test(this.value)) {
                    MT.widget.removeMessage();
                } else {
                    if (this.value !== "") {
                        showError(_53);
                    }
                }
                _4d.style.color = "#666666";
            });
            $E.on(_4c, "focus", function (e) {
                _4d.style.color = "#000000";
            });
            $E.on(_4f, "click", function (e) {
                selectType();
            });
            $E.on(_4e, "focus", function (e) {
                selectType();
            });
            $E.on(_4a, "submit", function (e) {
                $E.stopEvent(e);
                if (!canBuy(e, true)) {
                    return false;
                }
                if (_4c && !_52.test(_4c.value)) {
                    showError(_53);
                    _4c.focus();
                    return false;
                }
                if (_4e && _4e.value === "") {
                    showError(_54);
                    window.scrollTo(0, 0);
                    return false;
                }
                var _76 = $D.get("address-table");
                if (_76) {
                    if (MT.app.Deal.address.check()) {
                       // _4a["adrProvince"].value = _4a["adrProvince-c"].value;
                      //  _4a["adrCity"].value = _4a["adrCity-c"].value;
                      //  _4a["adrDistrict"].value = _4a["adrDistrict-c"].value;
                    } else {
                        return false;
                    }
                }
                var _77 = parseInt(_4b.value, 10);
                setValue(_50, _77);
                setValue(_51, _77);
                this.submit();
                return true;
            });
        }
        function selectType() {
            var _78 = $D.get("deal-buy-type-select");
            var _79 = MT.util.decodeJSON(_78.getAttribute("stype"));
            var _7a = $D.get("deal-buy-type-select-t");
            var _7b = $D.get("deal-buy-type-select-b");
            var _7c = "\uff0c";
            var _7d = "*";
            if (!_57) {
                generateDialog();
            } else {
                _57.show();
            }
            function generateDialog() {
                _57 = MT.util.getCommonDialog(_56, {
                    width: "450px",
                    fixedcenter: false
                });
                var _7e = ["<div class=\"\">"];
                _7e.push("<h3 class=\"head\">\u9009\u62e9\u7c7b\u578b<span class=\"close\">\u5173\u95ed</span></h3>");
                _7e.push("<div class=\"body" + (_79.length > 5 ? " long" : "") + "\">");
                _7e.push("<table class=\"list\" id=\"deal-type-select-list\">");
                _7e.push("<tr><th class=\"type\">\u7c7b\u578b</th><th class=\"desc\">\u8bf4\u660e</th><th class=\"quantity\">\u6570\u91cf</th></tr>");
                for (var i = 0; i < _79.length; i++) {
                    var t = _79[i];
                    var _81 = i % 2 ? " class=\"alt\"" : "";
                    _7e.push("<tr" + _81 + ">");
                    _7e.push("<td class=\"type\">");
                    _7e.push("<p class=\"text\">" + t["type"] + "</p>");
                    if (t["maxnumber"] && t["curnumber"] > t["maxnumber"] * 0.8) {
                        _7e.push("<p class=\"last\">\u5269\u4f59\u6570\u91cf\uff1a<strong>" + (t["maxnumber"] - t["curnumber"]) + "</strong></p>");
                    }
                    _7e.push("</td>");
                    _7e.push("<td class=\"desc\">");
                    _7e.push("<p class=\"text\">" + t["desc"] + "</p>");
                    _7e.push("</td>");
                    _7e.push("<td class=\"quantity\">");
                    _7e.push("<p class=\"input\">");
                    if (t["maxnumber"]) {
                        var _82 = t["maxnumber"] - t["curnumber"];
                        if (_82 > 0) {
                            _7e.push("<input type=\"text\" autocomplete=\"off\" class=\"f-text\" maxlength=\"4\" name=\"quantity\" value=\"\" stype=\"" + t["type"] + "\" smax=\"" + _82 + "\" />");
                        } else {
                            _7e.push("\u5356\u5149\u4e86");
                        }
                    } else {
                        _7e.push("<input type=\"text\" autocomplete=\"off\" class=\"f-text\" maxlength=\"4\" name=\"quantity\" value=\"\" stype=\"" + t["type"] + "\" />");
                    }
                    _7e.push("</p>");
                    _7e.push("<p class=\"error\"></p>");
                    _7e.push("</td>");
                    _7e.push("</tr>");
                }
                _7e.push("</table>");
                _7e.push("</div>");
                _7e.push("<p class=\"act\"><input id=\"deal-type-select-commit\" type=\"submit\" class=\"formbutton\" value=\"\u786e\u5b9a\" /></p>");
                _7e.push("</div>");
                _57.setContent(_7e.join(""));
                _57.open();
                _57.center();
                initEvent();
            }
            function initEvent() {
                var _83 = $D.get("deal-type-select-list");
                var _84 = $D.get("deal-type-select-commit");
                var _85 = $D.getElementsByClassName("f-text", "input", _83);
                var reg = /^\d+$/;
                var _87 = [];
                if (_7a.value !== "") {
                    var _88 = _7a.value.split(_7c);
                    for (var i = 0; i < _88.length; i++) {
                        var a = _88[i].split(_7d);
                        _87[a[0]] = a[1];
                    }
                }
                $D.batch(_85, function (el) {
                    var _8c = el.getAttribute("stype");
                    if (_87[_8c]) {
                        el.value = _87[_8c];
                    }
                    $E.on(el, "focus", function () {
                        checkTypeSelect(this);
                    });
                    $E.on(el, "blur", function () {
                        checkTypeSelect(this);
                    });
                });
                $E.on(_84, "click", function (e) {
                    var sum = 0;
                    var _8f = [];
                    for (var i = 0, len = _85.length; i < len; i++) {
                        var el = _85[i];
                        if (checkTypeSelect(el)) {
                            var _93 = parseInt(el.value, 10);
                            if (_93 > 0) {
                                sum += _93;
                                _8f.push(el.getAttribute("stype") + _7d + _93);
                            }
                        } else {
                            el.focus();
                            return;
                        }
                    }
                    if (sum > 0) {
                        _4b.value = sum;
                    }
                    _57.hide();
                    if (check(e)) {
                        _7a.value = _8f.join(_7c);
                    } else {
                        _4b.focus();
                    }
                });
            }
            function checkTypeSelect(el) {
                var _95 = el.value;
                var max = el.getAttribute("smax");
                var reg = /^\d+$/;
                if (!_95) {
                    hideError(el);
                    return true;
                }
                if (!reg.test(_95)) {
                    showError(el, "\u6570\u91cf\u5fc5\u987b\u4e3a\u6570\u5b57");
                    return false;
                }
                if (max) {
                    _95 = parseInt(_95, 10);
                    max = parseInt(max, 10);
                    if (_95 > max) {
                        showError(el, "\u8d85\u8fc7\u4e86\u5269\u4f59\u6570\u91cf");
                        return false;
                    }
                }
                hideError(el);
                return true;
            }
            function showError(el, _99) {
                var _9a = $D.getAncestorByTagName(el, "td");
                $D.getElementsByClassName("error", "p", _9a, function (i) {
                    i.innerHTML = _99;
                    i.style.display = "block";
                });
            }
            function hideError(el) {
                var _9d = $D.getAncestorByTagName(el, "td");
                $D.getElementsByClassName("error", "p", _9d, function (i) {
                    i.style.display = "none";
                });
            }
        }
        var _9f = $D.get("deal-buy-cardcode");
        var _a0 = $D.get("deal-buy-cardcode-login");
        var _a1 = $D.get("deal-buy-cardcode-signup");
        var _a2 = $D.get("cardcode-text");
        var _a3 = $D.get("cardcode-deal");
        var _a4 = $D.get("cardcode-verify");
        var _a5 = $D.get("cardcode-link");
        $E.on(_a5, "click", function (e) {
            _a5.blur();
            var el = $D.get("cardcode-link-t");
            var _a8 = $D.getStyle(el, "display") == "none";
            el.style.display = _a8 ? "block" : "none";
            if (_a8) {
                window.setTimeout(function () {
                    _a2.focus();
                }, 0);
            }
        });
        $E.on(_a4, "click", function (e) {
            if ($D.hasClass(_a4, "loading")) {
                return;
            }
            var _aa = _a2.value;
            var _ab = "cardcode=" + _aa + "&dealid=" + _a3.value;
            var _ac = $C.asyncRequest("POST", "/card/check", {
                success: function (o) {
                    var res = MT.util.getEvalRes(o);
                    if (res.status) {
                        _a4.style.visibility = "hidden";
			_a2.disabled = true;
                        $D.addClass(_a2, "readonly");
                        $D.get("cardcode-row").style.display = "";
                        $D.get("cardcode-row-t").innerHTML = res.data;
                        $D.get("cardcode-row-n").innerHTML = _aa;
                        setValue(_9f, _aa);
                        setValue(_a0, _aa);
                        setValue(_a1, _aa);
                        check(e);
                    } else {
                        showError(res.msg);
                        _a4.value = "\u786e\u5b9a";
                    }
                    $D.removeClass(_a4, "loading");
                    _a4.disabled = false;
                },
                failure: function (o) {}
            }, _ab);
            if ($C.isCallInProgress(_ac)) {
                $D.addClass(_a4, "loading");
                _a4.disabled = true;
                _a4.blur();
            }
        });
    };
    MT.app.Deal.address = (function () {
        var _b0, _b1, _b2, _b3, _b4, _b5, _b6, _b7, _b8;
        var _b9, _ba, _bb, _bc, _bd;
        var _be = null;
        var _bf = "\u8bf7\u9009\u62e9\u7701";//请选择省
        var _c0 = "\u8bf7\u9009\u62e9\u5e02";//请选择市
        var _c1 = "\u8bf7\u9009\u62e9\u5e02\u533a";//请选择市区
        var _c2 = "\u90ae\u653f\u7f16\u7801\u586b\u5199\u6709\u8bef,\u8bf7\u8f93\u51656\u4f4d\u90ae\u653f\u7f16\u7801";//邮政编码填写有误,请输入6位邮政编码
        var _c3 = "\u8bf7\u586b\u5199\u8857\u9053\u5730\u5740\uff0c\u6700\u5c115\u4e2a\u5b57\uff0c\u6700\u591a\u4e0d\u80fd\u8d85\u8fc760\u4e2a\u5b57\uff0c\u4e0d\u80fd\u5168\u90e8\u4e3a\u6570\u5b57";//请填写街道地址，最少5个字，最多不能超过60个字，不能全部为数字
        var _c4 = "\u8bf7\u6b63\u786e\u586b\u5199\u59d3\u540d\uff0c\u6700\u5c11\u4e0d\u80fd\u4f4e\u4e8e2\u4e2a\u5b57\uff0c\u6700\u591a\u4e0d\u80fd\u8d85\u8fc715\u4e2a\u5b57";//请正确填写姓名，最少不能低于2个字，最多不能超过15个字
        var _c5 = "\u7535\u8bdd\u53f7\u7801\u4e0d\u80fd\u5c11\u4e8e7\u4f4d";//电话号码不能少于7位

        function getAttr(_c6, _c7) {
            return (_c6 && _c6.getAttribute(_c7)) ? _c6.getAttribute(_c7) : "";
        }
        function checkProvince() {
            if (_b3.value === "") {
                _bb.innerHTML = _bf;
                _bb.style.display = "block";
                return false;
            } else {
                _bb.style.display = "none";
                return true;
            }
        }
        function checkCity() {
            if (_b4.options.length > 1 && _b4.value === "") {
                _bb.innerHTML = _c0;
                _bb.style.display = "block";
                return false;
            } else {
                _bb.style.display = "none";
                return true;
            }
        }
        function checkDistrict() {
            if (_b5.options.length > 1 && _b5.value === "") {
                _bb.innerHTML = _c1;
                _bb.style.display = "block";
                return false;
            } else {
                _bb.style.display = "none";
                return true;
            }
        }
        function checkZipcode() {
            if (!_b6.value || !_b6.value.match(/^\d{6}$/)) {
                _bc.innerHTML = _c2;
                _bc.style.display = "block";
                return false;
            } else {
                _bc.style.display = "none";
                return true;
            }
        }
        function checkDetail() {
            if (!_b2.value || (_b2.value.length < 5) || _b2.value.match(/^\d*$/)) {
                _ba.innerHTML = _c3;
                _ba.style.display = "block";
                return false;
            } else {
                _ba.style.display = "none";
                return true;
            }
        }
        function checkName() {
            if (!_b1.value || (_b1.value.length < 2) || (_b1.value.length > 15)) {
                _b9.innerHTML = _c4;
                _b9.style.display = "block";
                return false;
            } else {
                _b9.style.display = "none";
                return true;
            }
        }
        function checkPhone() {
            if (!_b7.value || !_b7.value.match(/^.{7,}$/)) {
                _bd.innerHTML = _c5;
                _bd.style.display = "block";
                return false;
            } else {
                _bd.style.display = "none";
                return true;
            }
        }
        function scrollToAddress() {
            window.scrollTo(0, $D.getY(_b8));
        }
        return {
            init: function () {
                _b8 = $D.get("address-table");
                if (!_b8) {
                    return;
                }
                _b0 = $D.get("deal-buy-form");
                _b1 = $D.get("address-name");
                _b9 = $D.get("address-name-error");
                _b2 = $D.get("address-detail");
                _ba = $D.get("address-detail-error");
                _b3 = $D.get("selProvinces");
                _b4 = $D.get("selCities");
                _b5 = $D.get("selDistricts");
                _bb = $D.get("address-district-error");
                _b6 = $D.get("address-zipcode");
                _bc = $D.get("address-zipcode-error");
                _b7 = $D.get("address-phone");
                _bd = $D.get("address-phone-error");
                //var _c8 = MT.data.location.relation;
                //var _c9 = MT.data.location.name;
                var _ca = _b8.getAttribute("isglobal") === "true";
                var _cb = _b8.getAttribute("sprovince");
                var _cc = _b8.getAttribute("scity");
                var _cd = _b8.getAttribute("sprovincelist");
                var _ce = _cd.length > 0 ? _cd.split(",") : null;
                var _cf = _b0["adrProvince"].value;
                var _d0 = _b0["adrCity"].value;
                var _d1 = _b0["adrDistrict"].value;
                var _d2 = $D.get("deal-buy-address-list");
                var _d3 = false;
                initCheck();
                if (!_d2) {
                    initLocation();
                    _d3 = true;
                    return;
                }
                initList();

                function initCheck() {
                    $E.on(_b3, "blur", function (e) {
                        checkProvince();
                    });
                    $E.on(_b4, "blur", function (e) {
                        checkCity();
                    });
                    $E.on(_b5, "blur", function (e) {
                        checkDistrict();
                    });
                    $E.on(_b6, "blur", function (e) {
                        checkZipcode();
                    });
                    $E.on(_b2, "blur", function (e) {
                        checkDetail();
                    });
                    $E.on(_b1, "blur", function (e) {
                        checkName();
                    });
                    $E.on(_b7, "blur", function (e) {
                        checkPhone();
                    });
                }
                function initList() {
                    var _db = _d2.getElementsByTagName("li");
                    var _dc = _d2.getElementsByTagName("input");
                    $D.batch(_dc, function (el) {
                        if (el.checked) {
                            _be = el.parentNode;
                            $D.addClass(el.parentNode, "selected");
                        }
                        $E.on(el, "click", function (e) {
                            var _df = this.parentNode;
                            if (!_d3) {
                                window.setTimeout(function () {
                                    initLocation();
                                    _d3 = true;
                                }, 0);
                            }
                            function __restore() {
                                _b1.value = getAttr(_be, "sname");
                                _b2.value = getAttr(_be, "saddress");
                                _b6.value = getAttr(_be, "szipcode");
                                _b7.value = getAttr(_be, "sphone");
                            }
                            if (this.id == "deal-buy-address-other") {
                                var _e0 = getAttr(_be, "scity");
                                var _e1 = getAttr(_be, "sprovince");
                                var _e2 = getAttr(_be, "scity");
                                var _e3 = getAttr(_be, "sdistrict");
                                if (_ca) {
                                    __restore();
                                    window.setTimeout(function () {
                                        createProvince(_e1);
                                        createCity(_e1, _e2);
                                        createDistrict(_e1, _e2, _e3);
                                    }, 0);
                                } else {
                                    if (_ce) {
                                        if (MT.util.inArray(_e1, _ce)) {
                                            __restore();
                                            createProvince(_e1);
                                            createCity(_e1, _e2);
                                            createDistrict(_e1, _e2, _e3);
                                        } else {
                                            createProvince();
                                            createCity();
                                            createDistrict();
                                        }
                                    } else {
                                        if (_cc == _e0) {
                                            __restore();
                                            window.setTimeout(function () {
                                                selectByValue(_b5, _e3);
                                            }, 0);
                                        } else {
                                            reset();
                                        }
                                    }
                                }
                                $D.getElementsByClassName("blk-error", "div", "address-table", function (el) {
                                    el.style.display = "none";
                                });
                                $D.get("address-table").style.display = "block";
                            } else {
                                $D.get("address-table").style.display = "none";
                                _be = _df;
                            }
                            $D.removeClass(_db, "selected");
                            $D.addClass(_df, "selected");
                        });
                    });
                }
                function initLocation() {
                    createProvince(_cf);
                    createCity(_cf, _d0);
                    createDistrict(_cf, _d0, _d1);
                    if (_ca) {
		    } else {
                        if (_ce) {
                            if (!MT.util.inArray(_cf, _ce)) {
                                if (_b3.options.length) {
                                    _b3.options[0].selected = true;
                                }
                                if (_b4.options.length) {
                                    _b4.options[0].selected = true;
                                }
                            }
                        } else {
                            //_b3.disabled = true;
			    //_b4.disabled = true;
                        }
                    }
                    $E.on(_b3, "change", function (e) {
                        var _e6 = this.options[this.selectedIndex];
                        var _e7 = _e6.value;
                        if (_e7) {
                            createCity(_e7);
                            if (_b4.options.length == 2) {
                                _b4.options[1].selected = true;
                                createDistrict(_e7, _b4.options[1].value);
                                if (_b5.options.length == 2) {
                                    _b5.options[1].selected = true;
                                }
                            } else {
                                _b5.options.length = 0;
                                appendOption(_b5);
                            }
                        } else {
                            _b4.options.length = 0;
                            _b5.options.length = 0;
                            appendOption(_b4);
                            appendOption(_b5);
                        }
                    });
                    $E.on(_b4, "change", function (e) {
                        var _e9 = this.options[this.selectedIndex];
                        if (_e9.value) {
                            createDistrict(_b3.value, _e9.value);
                        } else {
                            _b5.options.length = 0;
                            appendOption(_b5);
                        }
                    });
                    $E.on(_b5, "change", function (e) {});
                }
                function reset() {
                    _b1.value = "";
                    _b2.value = "";
                    _b6.value = "";
                    _b7.value = "";
                    if (_b5.options.length) {
                        _b5.options[0].selected = true;
                    }
                }
                function selectByValue(_eb, _ec) {
                    if (!_eb) {
                        return;
                    }
                    for (var i = 0; i < _eb.options.length; i++) {
                        var _ee = _eb.options[i];
                        if (_ee.value == _ec) {
                            _ee.selected = true;
                            break;
                        }
                    }
                }
                function createProvince(_ef) {
		    /*
                    var _f0 = false;
                    _b3.options.length = 0;
                    appendOption(_b3);
                    for (var i in _c8) {
                        if (_ce && !MT.util.inArray(i, _ce)) {
                            continue;
                        }
                        if (_ef) {
                            _f0 = i == _ef;
                        }
                        appendOption(_b3, i, _f0);
                    }
		    */
                }
                function createCity(_f2, _f3) {
		    /*
                    var _f4 = false;
                    _b4.options.length = 0;
                    if (_ce && !MT.util.inArray(_f2, _ce)) {
                        return;
                    }
                    appendOption(_b4);
                    var _f5 = _c8[_f2];
                    if (_f5) {
                        for (var i in _f5) {
                            if (_f3) {
                                _f4 = i == _f3;
                            }
                            appendOption(_b4, i, _f4);
                        }
                    }*/
                }
                function createDistrict(_f7, _f8, _f9) {/*
                    _b5.options.length = 0;
                    if (_ce && !MT.util.inArray(_f7, _ce)) {
                        return;
                    }
                    appendOption(_b5);
                    var _fa = _c8[_f7];
                    if (!_fa) {
                        return;
                    }
                    var _fb = _fa[_f8];
                    var _fc = false;
                    var _fd = new Date().getTime() < new Date(2010, 4, 24).getTime();
                    for (var i = 0, len = _fb.length; i < len; i++) {
                        if (_f9) {
                            _fc = _fb[i] == _f9;
                        }
                        if (_fb[i] === 310230 && _fd) {
                            continue;
                        }
                        appendOption(_b5, _fb[i], _fc);
                    }*/
                }
                function appendOption(el, id, _102) {/*
                    if (id) {
                        var text = _c9[id];
                        if (text) {
                            var opt = new Option(text, id);
                            if (_102) {
                                opt.selected = true;
                            }
                            el.options.add(opt);
                        }
                    } else {
                        el.options.add(new Option("--", ""));
                    }*/
                }
                function checkProvinceInArray(id) {
                    if (!id) {
                        return false;
                    }
                    if (_ce && _ce.length) {
                        return MT.util.inArray(id, _ce);
                    }
                    return true;
                }
            },
            check: function () {
                var _106 = $D.get("deal-buy-form");
                var _107 = MT.util.getRadioValue(_106, "addressid");
                if (!_b8 || _107 > 0) {
                    return true;
                }
                if (!checkProvince()) {
                    scrollToAddress();
                    return false;
                }
                if (!checkCity()) {
                    scrollToAddress();
                    return false;
                }
                if (!checkDistrict()) {
                    scrollToAddress();
                    return false;
                }
                if (!checkZipcode()) {
                    scrollToAddress();
                    return false;
                }
                if (!checkDetail()) {
                    scrollToAddress();
                    return false;
                }
                if (!checkName()) {
                    scrollToAddress();
                    return false;
                }
                if (!checkPhone()) {
                    scrollToAddress();
                    return false;
                }
                return true;
            }
        };
    })();
})();