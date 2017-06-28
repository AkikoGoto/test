<?php
//携帯版のみで利用するいろいろな関数

//携帯版のみ、UTF-8のデータをShift_JISにするための関数

function Encode_utf8tosjis ($tpl_output, &$smarty) {

	$tpl_output = mb_convert_encoding($tpl_output, 'Shift_JIS', 'UTF-8');

	return $tpl_output;

}




?>