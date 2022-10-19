function slides(){var defaultOpts = { interval: 5000, fadeInTime: 300, fadeOutTime: 200 };
//Iterate over the current set of matched elements
	var _titles = $("ul.slide-txt li");
	var _titles_bg = $("ul.op li");
	var _bodies = $("ul.slide-pic li");
	var _count = _titles.length;
	var _current = 0;
	var _intervalID = null;
	var stop = function() { window.clearInterval(_intervalID); };
	var slide = function(opts) {
		if (opts) {
			_current = opts.current || 0;
		} else {
			_current = (_current >= (_count - 1)) ? 0 : (++_current);
		};
		_bodies.filter(":visible").fadeOut(defaultOpts.fadeOutTime, function() {
			_bodies.eq(_current).fadeIn(defaultOpts.fadeInTime);
			_bodies.removeClass("cur").eq(_current).addClass("cur");
		});
		_titles.removeClass("cur").eq(_current).addClass("cur");
		_titles_bg.removeClass("cur").eq(_current).addClass("cur");
	}; //endof slide
	var go = function() {
		stop();
		_intervalID = window.setInterval(function() { slide(); }, defaultOpts.interval);
	}; //endof go
	var itemMouseOver = function(target, items) {
		stop();
		var i = $.inArray(target, items);
		slide({ current: i });
	}; //endof itemMouseOver
	_titles.hover(function() { if($(this).attr('class')!='cur'){itemMouseOver(this, _titles); }else{stop();}}, go);
	//_titles_bg.hover(function() { itemMouseOver(this, _titles_bg); }, go);
	_bodies.hover(stop, go);
	//trigger the slidebox
	go();
}
function drag(o,s)
{
    if (typeof o == "string") o = document.getElementById(o);
    o.orig_x = parseInt(o.style.left) - document.body.scrollLeft;
    o.orig_y = parseInt(o.style.top) - document.body.scrollTop;
    o.orig_index = o.style.zIndex;

    o.onmousedown = function(a)
    {
	this.style.cursor = "move";
	this.style.position='absolute';
	this.style.zIndex = 10000;
	var d=document;
	if(!a)a=window.event;
	var x = a.clientX+d.body.scrollLeft-o.offsetLeft;
	var y = a.clientY+d.body.scrollTop-o.offsetTop;
	//author: www.longbill.cn
	d.ondragstart = "return false;"
	d.onselectstart = "return false;"
	d.onselect = "document.selection.empty();"

	if(o.setCapture)
	    o.setCapture();
	else if(window.captureEvents)
	    window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);

	d.onmousemove = function(a)
	{
	    if(!a)a=window.event;
	    o.style.left = a.clientX+document.body.scrollLeft-x;
	    //o.style.top = a.clientY+document.body.scrollTop-y;
	    o.orig_x = parseInt(o.style.left) - document.body.scrollLeft;
	    //o.orig_y = parseInt(o.style.top) - document.body.scrollTop;
	}

	d.onmouseup = function()
	{
	    if(o.releaseCapture)
		o.releaseCapture();
	    else if(window.captureEvents)
		window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
	    d.onmousemove = null;
	    d.onmouseup = null;
	    d.ondragstart = null;
	    d.onselectstart = null;
	    d.onselect = null;
	    o.style.cursor = "normal";
	    o.style.zIndex = o.orig_index;
	}
    }

    if (s)
    {
	var orig_scroll = window.onscroll?window.onscroll:function (){};
	window.onscroll = function ()
	{
	    orig_scroll();
	    o.style.left = o.orig_x + document.body.scrollLeft;
	    //o.style.top = o.orig_y + document.body.scrollTop;
	}
    }
}