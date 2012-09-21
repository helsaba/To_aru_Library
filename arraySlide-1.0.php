<?php

//■Array Slide Library Ver1.0■//
//
//●概要
// * array_slide関数 *
//
//第1引数に指定した配列の、第2引数に指定したキーの要素を、
//第3引数に指定した分だけずらした配列を返します。
//存在する領域を超えてずらそうとすると自動的に補正されます。
//第4引数の「search target with order」オプションをTrueに設定した場合、
//第2引数に指定された値で「キー」に関係なく「番目」をもとに検索します。
//ずらす対象が存在しなかったときはFalseが返されます。
//
//
//●例
//$arr = array("ド"=>"ドーナツ","レ"=>"レモン","ミ"=>"ミカン","ファ"=>"ふぁぼれよ","ソ"=>"蒼井そら");
//
//
//
//例1. $arrのキー「レ」に該当する要素の位置を後ろに2ずらす
//
//var_dump(array_slide($arr,"レ",2));
//
//結果1.
//
//array("ド"=>"ドーナツ","ミ"=>"ミカン","ファ"=>"ふぁぼれよ","レ"=>"レモン","ソ"=>"蒼井そら")
//
//
//
//例2. $arrのキー「ソ」に該当する要素の位置を手前に50ずらす
//(領域を超えるので自動的に補正される)
//
//var_dump(array_slide($arr,"ソ",-50));
//
//結果2.
//
//array("ソ"=>"蒼井そら","ド"=>"ドーナツ","レ"=>"レモン","ミ"=>"ミカン","ファ"=>"ふぁぼれよ")
//
//
//
//例3. $arrの(0から数えて)3番目の要素の位置を手前に2ずらす
//
//var_dump(array_slide($arr,3,-2,True));
//
//結果3.
//
//array("ド"=>"ドーナツ","ファ"=>"ふぁぼれよ","レ"=>"レモン","ミ"=>"ミカン","ソ"=>"蒼井そら")
//
//
//

function array_slide($array,$key,$amount,$search_target_with_order=false) {
	
	//キーを失わないように次元を上げ、連想配列でない配列を作る
	$cnt=0;
	foreach ($array as $_key => $value) {
		//オプションの有無で場合分け
		switch($search_target_with_order) {
			case false:
				if ($_key==$key) {
					//ターゲット取得
					$target = array($_key=>$value);
					//番目を取得
					$pos = $cnt;
				}
				break;
			default:
				if ($cnt===$key) {
					//ターゲット取得
					$target = array($_key=>$value);
					//番目を取得
					$pos = $cnt;
				}
		}
		$parent[] = array($_key=>$value);
		$cnt++;
	}
	
	//ターゲットが見つからなかったときはFalseを返す
	if (is_null($target)) return false;
	
	//個数をカウント
	$count = count($parent);
	
	//必要以上にスライドする場合、必要最小限のスライド量に修正
	$new_pos = $pos + $amount;
	if ($new_pos < 0) $new_pos = 0;
	elseif ($new_pos >= $count) $new_pos = $count - 1;
	$amount = $new_pos - $pos;
	
	//要素のずらす量で場合分けし、一時的に「追加」の形を取る
	switch (true) {
		
		//±0
		case $amount == 0 :
			return $array;
		
		//＋
		case $amount > 0 :
			//目的の「前後関係」を得るために更に1スライド
			array_splice($parent,$new_pos+1,0,array($target));
			//後に重複する部分を後ろから削除するため一時的に反転
			$parent = array_reverse($parent);
			break;
			
		//－
		default :
			array_splice($parent,$new_pos,0,array($target));
			
	}
	
	//上げた次元をもとに戻す
	foreach ($parent as $child) {
		foreach ($child as $_key => $value) {
			$new_arr[$_key] = $value;
		}
	}
	
	//重複する要素を削除し、実際にずらした配列を得る
	$new_arr = array_unique($new_arr);
	
	//一時的に反転させていた場合のみもとに戻す
	if ($amount > 0) $new_arr = array_reverse($new_arr);
	
	//配列を返す
	return $new_arr;
	
}

?>