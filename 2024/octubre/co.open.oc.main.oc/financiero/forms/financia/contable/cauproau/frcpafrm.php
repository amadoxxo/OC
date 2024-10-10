<?php 
  namespace openComex;
switch ($gFunction) {
	case "cDocId_DO":
	 $cRuta = "frcpados.php?";
	 $cRuta .= "gTerTip=$gTerTip";
   $cRuta .= "&gTerId=$gTerId";
   $cRuta .= "&gTerTipB=$gTerTipB";
   $cRuta .= "&gTerIdB=$gTerIdB";
   $cRuta .= "&gSecuencia=$gSecuencia";
   $cRuta .= "&gDo=$gDo";
	break;
	default:
	 $cRuta  = "frcpacnt.php?";
	 $cRuta .= "gTerTip=$gTerTip";
   $cRuta .= "&gTerId=$gTerId";
   $cRuta .= "&gTerTipB=$gTerTipB";
   $cRuta .= "&gTerIdB=$gTerIdB";
   $cRuta .= "&gPucId=$gPucId";
   $cRuta .= "&gPucDet=$gPucDet";
   $cRuta .= "&gPucTipEj=$gPucTipEj";   
   $cRuta .= "&gComNit=$gComNit";
   $cRuta .= "&gSecuencia=$gSecuencia";
   $cRuta .= "&gDo=$gDo";
	break;
}
?>
<html>
	<head>
		<title></title>
	</head>
	<frameset rows="*,0" border=0 framespacing=0 frameborder=0>
		<frame src="<?php echo $cRuta ?>"
			name="framework" 
			frameborder=0 
			border=0 
			framespacing=0 
			marginheight=0 
			marginwidth=0 
			scrolling="Si" 
			noresize>
		<frame src="" 
			name="framepro" 
			frameborder=0 
			border=0 
			framespacing=0 
			marginheight=0 
			marginwidth=0 
			scrolling="No" 
			noresize>
	</frameset>
</html>