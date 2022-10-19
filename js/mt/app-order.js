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
    MT.app.Order = {
	_noticeDialog: null,
	getNoticeDialog: function () {
	    if (!this._noticeDialog) {
		var _c = {
		    width: "344px",
		    fixedcenter: true,
		    close: false,
		    draggable: false,
		    zindex: 7,
		    modal: true,
		    visible: false
		};
		this._noticeDialog = new YAHOO.widget.Panel("order-pay-dialog", _c);
	    }
	    return this._noticeDialog;
	},
	pay: function () {},
	check: function () {
	    var _d = this;
	    var _e = $D.get("order-pay-button");
	    var _f = $D.get("order-check-typelist");
	    var _10 = $D.get("order-check-banktable");
	    var _11 = [];
	    if (_f) {
		_11 = $D.getElementsBy(function (el) {
		    return el.type === "radio";
		}, "input", _f, function (el) {
		    $E.on(el, "click", function (e) {
			el.blur();
			$D.batch(_11, function (i) {
			    i.checked = false;
			});
			this.checked = true;
			hideTips();
			if (el.name === "paytype") {
			    $D.getElementsByClassName("order-check-tip", "p", this.parentNode, function (i) {
				i.style.display = "block";
			    });
			}
		    });
		});
	    }
	    if (_10) {
		var _17 = _10.getElementsByTagName("a");
		$D.batch(_17, function (el) {
		    $E.on(el, "click", function (e) {
			el.blur();
			$D.batch(_11, function (i) {
			    i.checked = false;
			});
			var _1b = el.getElementsByTagName("input")[0];
			_1b.checked = true;
			hideTips();
		    });
		});
	    }
	    $E.on(_e, "click", function (e) {
		var _1d = $D.get("order-pay-form-credit");
		var _1e;
		if (_1d) {
		    _1d.submit();
		} else {
                    			var _1f = $D.get("check-alipay");
			var _20 = $D.get("check-chinabank");
			var _21 = $D.get("check-tenpay");
			var _22;
			if (_1f && _1f.checked) {
			    _1e = $D.get("order-pay-form-alipay");
			} else {
			    if (_20 && _20.checked) {
				_1e = $D.get("order-pay-form-chinabank");
			    } else {
				if (_21 && _21.checked) {
				    _1e = $D.get("order-pay-form-tenpay");
				} else {
				    if ($D.get("order-pay-form-alipay-bank")) {
					_1e = $D.get("order-pay-form-alipay-bank");
					_22 = MT.util.getRadioValue(_1e, "defaultbank");
					if (!_22) {
					    alert("\u8bf7\u9009\u62e9\u652f\u4ed8\u65b9\u5f0f");//请选择支付方式
					    return;
					}
				    } else {
					if ($D.get("order-pay-form-tenpay-bank")) {
					    _1e = $D.get("order-pay-form-tenpay-bank");
					    _22 = MT.util.getRadioValue(_1e, "bank_type");
					    if (!_22) {
						alert("\u8bf7\u9009\u62e9\u652f\u4ed8\u65b9\u5f0f");//请选择支付方式
						return;
					    }
					} else {
					    return;
					}
				    }
				}
			    }
			}
			var _23 = _1e.getAttribute("stype");
			var id = _1e.getAttribute("sid");
			//var id=res.msg;
			if (_7) {
			    window.setTimeout(function () {
				_1e.submit();
			    }, 300);
			    showNotice(id);
			} else {
			    if (_8) {
				_1e.submit();
				window.setTimeout(function () {
				    showNotice(id);
				}, 300);
			    } else {
				_1e.submit();
				showNotice(id);
			    }
			}
			
		    //修改一下
		    //var buyform = $D.get("buyForm");
		    //log(buyform);
		}
	    });

	    function showNotice(id) {
		var _26 = _d.getNoticeDialog();
		var _27 = ["<div class=\"order-pay-dialog-c\">"];
		var _28;
		if (id && id > 0) {
		    _28 = "check.php?id=" + id;
		} else {
		    _28 = "account.php?act=credit";
		}
		_27.push("<h3><span id=\"order-pay-dialog-close\" class=\"close\">\u5173\u95ed</span></h3>");
		_27.push("<p class=\"info\">\u8bf7\u60a8\u5728\u65b0\u6253\u5f00\u7684\u9875\u9762\u4e0a<br />\u5b8c\u6210\u4ed8\u6b3e\u3002</p>");
		_27.push("<p class=\"notice\">\u4ed8\u6b3e\u5b8c\u6210\u524d\u8bf7\u4e0d\u8981\u5173\u95ed\u6b64\u7a97\u53e3\u3002<br />\u5b8c\u6210\u4ed8\u6b3e\u540e\u8bf7\u6839\u636e\u60a8\u7684\u60c5\u51b5\u70b9\u51fb\u4e0b\u9762\u7684\u6309\u94ae\uff1a</p>");
		_27.push("<p class=\"act\"><input id=\"order-pay-dialog-succ\" type=\"submit\" class=\"formbutton\" value=\"\u5df2\u5b8c\u6210\u4ed8\u6b3e\" /> <input id=\"order-pay-dialog-fail\" type=\"submit\" class=\"formbutton\" value=\"\u4ed8\u6b3e\u9047\u5230\u95ee\u9898\" /></p>");
		_27.push("<p class=\"retry\"><a href=\"javascript:void(0)\" id=\"order-pay-dialog-back\" class=\"back\">&raquo; \u8fd4\u56de\u9009\u62e9\u5176\u4ed6\u652f\u4ed8\u65b9\u5f0f</p>");
		_27.push("</div>");
		_26.setBody(_27.join(""));
		_26.render(document.body);
		_26.show();
		$E.on("order-pay-dialog-succ", "click", reload);
		$E.on("order-pay-dialog-fail", "click", reload);
		$E.on("order-pay-dialog-close", "click", reload);
		$E.on("order-pay-dialog-back", "click", function (e) {
		    _26.hide();
		});

		function reload() {
		    _26.hide();
		    window.location.href = _28;
		}
	    }
	    function hideTips() {
		function hide(el) {
		    el.style.display = "none";
		}
		$D.getElementsByClassName("order-check-tip", "p", _f, hide);
	    }
	    function log(_2b) {//改为添加订单/tun
		var url = "buy.php?a=pay";
		
		$C.setForm(_2b);
		var _2d = $C.asyncRequest("POST", url, {
		    success: function (o) {
			//修改订单的sid
			//var res = MT.util.getEvalRes(o);
			//var aa=$D.get("order-pay-form-alipay");
			//var tt=$D.get("order-pay-form-tenpay");
			//var ab=$D.get("order-pay-form-alipay-bank");
			//aa.setAttribute("sid",res.msg);
			//tt.setAttribute("sid",res.msg);
			//ab.setAttribute("sid",res.msg);









		    },
		    failure: function (o) {}
		});
	    }
	},
	cancel: function (url) {
	    var _31 = "\u4f60\u786e\u5b9a\u53d6\u6d88\u8fd9\u4e2a\u8ba2\u5355\u561b\uff1f";//你确定取消这个订单嘛？
	    $D.getElementsByClassName("order-cancel", "a", "order-list", function (el) {
		$E.on(el, "click", function (e) {
		    $E.stopEvent(e);
		    if (!window.confirm(_31)) {
			return;
		    }
		    var _34 = $C.asyncRequest("POST", el.href, {
			success: function (o) {
			    handleResponse(o, el);
			},
			failure: function (o) {}
		    });
		});
	    });

	    function handleResponse(o, el) {
		var res = MT.util.getEvalRes(o);
		if (res.status) {
		    el.parentNode.innerHTML = "\u5df2\u6210\u529f\u53d6\u6d88";
		} else {
		    alert(res.msg);
		}
	    }
	},
	feedback: function () {
	    var _3a = null;
	    var _3b = "order-feedback-dialog";
	    var _3c = MT.util.getCommonDialog(_3b, {
		width: "375px"
	    });
	    _3c.hideEvent.subscribe(function () {
		window.clearTimeout(_3a);
	    });
	    $D.getElementsByClassName("order-feedback", "a", "order-list", function (el) {
		$E.on(el, "click", function (e) {
		    $E.stopEvent(e);
		    showDialog(this);
		});
	    });

	    function showDialog(el) {
		if (!el) {
		    return;
		}
		var _40 = el.innerHTML === "\u8bc4\u4ef7";
		var _41 = el.getAttribute("sid");
		var _42 = el.getAttribute("score");
		var _43 = el.getAttribute("wantmore");
		var _44 = el.getAttribute("comment");
		var _45 = ["<div class=\"order-feedback-dialog\">"];
		_45.push("<h3 class=\"head\">" + (_40 ? "\u6211\u8981\u8bc4\u4ef7" : "\u4fee\u6539\u8bc4\u4ef7") + "<span  class=\"close\">\u5173\u95ed</span></h3>");
		_45.push("<div class=\"body\">");
		_45.push("<div id=\"order-feedback-dialog-error\" class=\"error\" style=\"display:none;\"></div>");
		_45.push("<form id=\"order-feedback-form\" action=\"/order/feedback/" + _41 + "\" method=\"post\">");
		_45.push("<div class=\"score\">\u7ed9\u672c\u5546\u5bb6\u6253\u5206<br />");
		_45.push("<span id=\"feedback-rating-bar\" class=\"rating-bar\">" + buildRateHTML(_42) + "</span><span id=\"order-feedback-intro\" class=\"intro\">" + getText(_42) + "</span>");
		_45.push("</div>");
		_45.push("<p class=\"want-more\">\u662f\u5426\u613f\u610f\u518d\u6b21\u53bb\u5546\u5bb6\u6d88\u8d39\uff1f<br />");
		_45.push("<input " + (_43 == "1" ? "checked=\"checked\"" : "") + " type=\"radio\" name=\"wantmore\" id=\"order-feedback-wantmore1\" value=\"1\" /><label for=\"order-feedback-wantmore1\">\u662f</label>");
		_45.push("<input " + (_43 == "0" ? "checked=\"checked\"" : "") + " type=\"radio\" name=\"wantmore\" id=\"order-feedback-wantmore0\" value=\"0\" /><label for=\"order-feedback-wantmore0\">\u5426</label>");
		_45.push("</p>");
		_45.push("<p class=\"comment\"><label for=\"order-feedback-suggest\">\u8bc4\u8bba</label><textarea cols=\"30\" rows=\"5\" name=\"comment\" id=\"order-feedback-suggest\" class=\"f-textarea\">" + _44 + "</textarea></p>");
		_45.push("<p class=\"act\"><input id=\"order-feedback-dialog-save\" type=\"submit\" class=\"formbutton\" value=\"\u4fdd\u5b58\" />");
		_45.push("<input type=\"hidden\" name=\"score\" value=\"" + _42 + "\" /></p>");
		_45.push("</form></div></div>");
		_3c.setContent(_45.join(""));
		_3c.open();
		var _46 = new MT.widget.Rating("feedback-rating-bar");
		var _47 = $D.get("order-feedback-form");
		var _48 = $D.get("order-feedback-intro");
		var _49 = false;
		var _4a = getText(_42);
		_46.onMouseOver.subscribe(function (t, a) {
		    _49 = false;
		    _48.innerHTML = getText(a[0].innerHTML);
		});
		_46.onMouseOut.subscribe(function (t, a) {
		    if (!_49) {
			_48.innerHTML = _4a;
		    }
		});
		_46.onSelect.subscribe(function (t, a) {
		    _49 = true;
		    _4a = getText(a[0]);
		    _48.innerHTML = _4a;
		    _47.score.value = this.currentScore;
		    hideError();
		});
		$E.on(_47, "submit", function (e) {
		    $E.stopEvent(e);
		    if (this.score.value < 1) {
			showError("\u8bf7\u7ed9\u5546\u5bb6\u6253\u5206");
			return;
		    }
		    var _52 = MT.util.parseStr($C.setForm(this));
		    var _53 = $C.asyncRequest("POST", _47.action, {
			success: function (o) {
			    handleResponse(o, el, _52, _40);
			},
			failure: function (o) {}
		    });
		});
	    }
	    function showError(msg) {
		var _57 = $D.get("order-feedback-dialog-error");
		if (_57 && msg) {
		    _57.innerHTML = msg;
		    _57.style.display = "block";
		}
	    }
	    function hideError() {
		var _58 = $D.get("order-feedback-dialog-error");
		if (_58) {
		    _58.style.display = "none";
		}
	    }
	    function getText(_59) {
		var arr = {
		    "1": "\u5f88\u4e0d\u6ee1\u610f",
		    "2": "\u4e0d\u6ee1\u610f",
		    "3": "\u4e00\u822c",
		    "4": "\u6ee1\u610f",
		    "5": "\u5f88\u6ee1\u610f"
		};
		return arr[_59] ? arr[_59] : "\u3000";
	    }
	    function handleResponse(o, el, _5d, _5e) {
		var res = MT.util.getEvalRes(o);
		var _60 = 3;
		var _61 = "<div class=\"succ\"><h4>" + (_5e ? "\u8bc4\u4ef7" : "\u4fee\u6539") + "\u6210\u529f\uff01</h4></div>";
		var _62 = "<div class=\"new\"><h4>\u8bc4\u4ef7\u6210\u529f\uff01</h4><p>\u83b7\u8d60 <strong>%d</strong> \u79ef\u5206\uff0c<a href=\"/account/points\" target=\"_blank\">\u67e5\u770b\u6211\u7684\u79ef\u5206</a></p></div>";
		if (res.status) {
		    el.setAttribute("score", _5d.score);
		    el.setAttribute("wantmore", _5d.wantmore);
		    el.setAttribute("comment", decodeURIComponent(_5d.comment));
		    var _63 = el.getAttribute("sid");
		    var _64 = $D.get("feedback-score-" + _63);
		    _64.innerHTML = buildRateHTML(_5d.score);
		    if (_64.style.display === "none") {
			_64.style.display = "block";
			el.innerHTML = "\u4fee\u6539\u8bc4\u4ef7";
			el.style.color = "";
		    }
		    var _65 = $D.getElementsByClassName("body", "div", _3b)[0];
		    if (res.data.point) {
			_65.innerHTML = _62.replace("%d", res.data.point);
		    } else {
			_65.innerHTML = _61;
		    }
		    _3c.fixOverlay();
		} else {
		    alert(res.msg);
		}
		_3a = window.setTimeout(function () {
		    _3c.hide();
		}, 1000 * _60);
	    }
	    function yellowFadeIn(el) {
		var _67 = $D.getAncestorByTagName(el, "tr");
		var _68 = _67.childNodes;
		var _69 = $D.hasClass(_67, "alt") ? "#f1f1f1" : "#ffffff";
		for (var i = 0, len = _68.length; i < len; i++) {
		    var _6c = _68[i];
		    if (_6c && _6c.nodeType == 1) {
			MT.util.Effect.yellowFadeIn(_6c, function (i) {
			    i.style.backgroundColor = _69;
			});
		    }
		}
	    }
	    function buildRateHTML(_6e) {
		var _6f = [];
		_6f.push("<span class=\"rating-star rating-star" + _6e + "\"></span>");
		_6f.push("<span class=\"rating-box\">");
		_6f.push("<a class=\"rating-link rating-link1\" href=\"javascript:void(0);\">1</a>");
		_6f.push("<a class=\"rating-link rating-link2\" href=\"javascript:void(0);\">2</a>");
		_6f.push("<a class=\"rating-link rating-link3\" href=\"javascript:void(0);\">3</a>");
		_6f.push("<a class=\"rating-link rating-link4\" href=\"javascript:void(0);\">4</a>");
		_6f.push("<a class=\"rating-link rating-link5\" href=\"javascript:void(0);\">5</a>");
		_6f.push("</span>");
		return _6f.join("");
	    }
	},
	refund: function () {
	    var _70 = $D.get("refund-apply");
	    var _71 = $D.get("refund-apply-form");
	    $E.on(_70, "click", function (e) {
		var _73 = $E.getTarget(e);
		var _74 = _73.nodeName.toLowerCase();
		if (_74 === "a" && $D.hasClass(_73, "refund-link")) {
		    _71.style.display = "block";
		}
	    });
	    $E.on(_71, "submit", function (e) {
		$E.stopEvent(e);
		$C.setForm(this);
		var _76 = $C.asyncRequest("POST", _71.action, {
		    success: handleReponse,
		    failure: function (o) {}
		});
	    });

	    function handleReponse(o) {
		var res = MT.util.getEvalRes(o);
		if (res.status) {
		    $D.getElementsByClassName("info", "p", _70, function (el) {
			el.innerHTML = "\u5df2\u7533\u8bf7\u5c06\u73b0\u91d1\u652f\u4ed8\u90e8\u5206\u9000\u6b3e\u5230\u94f6\u884c\uff0c\u5f85\u5904\u7406  <a href=\"javascript:void(0);\" class=\"refund-link\">\u4fee\u6539\u7533\u8bf7\u4fe1\u606f</a>";
		    });
		    _71.style.display = "none";
		} else {
		    alert(res.msg);
		}
	    }
	}
    };
})();