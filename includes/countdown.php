<?php

	$target_date = mktime(17,0,0,9,10,2020); //Data do lançamento [h:m:s:mês:dia:ano];  NOTA: não por zeros á esquerda

	$currDate = time();

	$diff = $target_date - $currDate; //Diferença entre a data de lançamento e a data do servidor em segundos

	if($diff == 1)
		echo '<script type="text/javascript">location.reload(true);</script>';
?>