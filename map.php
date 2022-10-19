<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
body{padding:0;margin:0;}
</style>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAOJyWp0sZye_p3XqQ41qjQBSNakcXOkKM7GVFJdCWJkNvCcIWDxRZOCQk-yalb9iir0Xd76DK0IYd6A" type="text/javascript"></script>
</head>

<body>
<div id="map" style="height:420px;width:720px;"></div>
</body>

<script type="text/javascript">
var map = new GMap2(document.getElementById("map"));
map.setCenter(new GLatLng(<?php echo $_GET['east'],',',$_GET['west'];?> ), 13); 
var latlng = new GLatLng(<?php echo $_GET['east'],',',$_GET['west'];?>); 
map.addOverlay(new GMarker(latlng));
</script>
</html>
