<?php

//■Array Slide Library Ver2.1■//
//
//●概要
// * array_slide関数 *
//
//第1引数に指定した配列の、第2引数に指定したキーの要素を、
//第3引数に指定した分だけずらします。
//成功した場合はTrue、失敗した場合はFalseを返します。
//存在する領域を超えてずらそうとすると自動的に補正されます。
//第4引数の「search target with order」オプションをTrueに設定した場合、
//第2引数に指定された値で「キー」に関係なく「番目」をもとに検索します。
//
//
//◆Ver2.1
//・オブジェクトにも対応できるようにメソッド、プロセスの順序を変更
//
//◆Ver2.0
//・第1引数を参照渡しに変更/返り値を変更
//(参照渡しなのでエラー処理を丁寧に書いた)
//
//
//
//●例
//$arr = array("ド"=>"ドーナツ","レ"=>"レモン","ミ"=>"ミカン","ファ"=>"ふぁぼれよ","ソ"=>"蒼井そら");
//
//
//
//例1. $arrのキー「レ」に該当する要素の位置を後ろに2ずらす
//
//array_slide($arr,"レ",2);
//var_dump($arr);
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
//array_slide($arr,"ソ",-50);
//var_dump($arr);
//
//結果2.
//
//array("ソ"=>"蒼井そら","ド"=>"ドーナツ","レ"=>"レモン","ミ"=>"ミカン","ファ"=>"ふぁぼれよ")
//
//
//
//例3. $arrの(0から数えて)3番目の要素の位置を手前に2ずらす
//
//array_slide($arr,3,-2,True);
//var_dump($arr);
//
//結果3.
//
//array("ド"=>"ドーナツ","ファ"=>"ふぁぼれよ","レ"=>"レモン","ミ"=>"ミカン","ソ"=>"蒼井そら")
//
//
//

function array_slide(&$array,$key,$amount,$search_target_with_order=false) {
	
	//引数が正しいかどうか判定
	if (!is_array($array) || !is_integer($amount) || !is_bool($search_target_with_order)) return false;

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
			//ずらす必要が無いときも、引数が正しければ成功と見なす
			return true;
		
		//＋
		case $amount > 0 :
			//目的の「前後関係」を得るために更に1スライド
			array_splice($parent,$new_pos+1,0,array($target));
			//後に重複する部分を後ろから削除するため一時的に反転
			$parent = array_reverse($parent);
			//キー番号を振り直す
			array_values($parent);
			break;
			
		//－
		default :
			array_splice($parent,$new_pos,0,array($target));
			
	}
	
	//重複する要素を削除し、実際にずらした配列を得る
	//(オブジェクトにも対応できるようにarray_uniqueは使わない)
	$haystack = array();
	$count++; //最初より1つ増えている
	for ($cnt=0;$cnt<$count;$cnt++) {
		if (in_array($parent[$cnt],$haystack)) unset($parent[$cnt]);
		else $haystack[] = $parent[$cnt];
	}
	
	//一時的に反転させていた場合のみもとに戻す
	if ($amount > 0) $parent = array_reverse($parent);
	
	//上げた次元をもとに戻す
	foreach ($parent as $child) {
		foreach ($child as $_key => $value) {
			$new_arr[$_key] = $value;
		}
	}
	
	//配列を渡す
	$array = $new_arr;
	
	return true;
	
}

?>