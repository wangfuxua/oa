<?
/*-------------得到时间的时和分---------*/
function getHi($dtime="0000-00-00 H:i:s"){
	
  $dtime=strtok($dtime," ");
  $dtime=strtok(" ");
  $dtime=substr($dtime,0,5);
  
  return $dtime;
  
	
}
function getHis($dtime="0000-00-00 H:i:s"){
	
  $dtime=strtok($dtime," ");
  $dtime=strtok(" ");
  //$dtime=substr($dtime,0,5);
  
  return $dtime;
  
	
}

?>