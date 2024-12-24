<?
	set_time_limit(0);
	$i = 0;
while(1){
	$c = $i * 60;
	sleep($c);
	echo $c.'<br>';
	flush();
	$i++;
}