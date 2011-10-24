<?php
/*
	should see this:
	
    To encode: 101010010000000001010101
    Encoded: jnjnjbja9jnjnjnj
    Decoded back: 101010010000000001010101

*/
	
require_once( "rle_url.php" );

$RLE_URL = new RLE_URL();

$to_encode = "101010010000000001010101";

$to_decode = $RLE_URL->encode( $to_encode );
$decoded = $RLE_URL->decode( $to_decode );

?>
<html>

<head>
<script type="text/javascript" src="rle_url.js"></script>
<script type="text/javascript">
var to_encode = "101010010000000001010101";
function hrm_try_this() {
	RLE_URL = new RLE_URL();
	var to_decode = RLE_URL.encode( to_encode );
	var decoded = RLE_URL.decode( to_decode );
	
	document.getElementById('e1').innerHTML = to_encode;
	document.getElementById('e2').innerHTML = to_decode;
	document.getElementById('e3').innerHTML = decoded;
}
</script>
</head>

<body onload="hrm_try_this()">

<h4>In PHP:</h4>
<ul>
	<li>To encode: <?php echo $to_encode ?></li>
	<li>Encoded: <?php echo $to_decode ?></li>
	<li>Decoded back: <?php echo $decoded ?></li>
</ul>

<h4>In JavaScript:</h4>
<ul>
	<li>To encode: <span id="e1"></span></li>
	<li>Encoded: <span id="e2"></span></li>
	<li>Decoded back: <span id="e3"></span></li>
</ul>

</body>

</html>
