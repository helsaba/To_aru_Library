<?php

//■Virtual Form Library Ver2.1■//
//
//簡単にaタグでPOSTが出来るリンクを張れます。
//多次元配列に対応しています。
//JavaScriptが使えない場合はSubmitボタンで表示します。
//「postForm_1」「postForm_2」「postForm_3」…という風にフォームに名前をつけていくので、
//これらと重複するフォームを作らないように注意してください。
//
//◆Ver2.1
////・半角スペースも置換対象に
//
//◆Ver2.0
////・多次元配列に対応
////・関数をcreateLinkのみに変更
////・データ型をhiddenのみに限定
////・シングル/ダブルクオーテーションが含まれていた場合指定文字列に置換する処理を導入
//
//◆Ver1.1
////・target属性を追加
//
/*

●例↓

require_once("./VirtualForm.php");

$vf = new VirtualForm; //ファイルの中で初めに1回だけインスタンスを生成

$data = 
	array(
		"name"=>
			"名前",
		"tokens"=>
			array(
				"access_token"=>"xxxxxxxxxxxxx",
				"access_token_secret"=>"yyyyyyyyyyyyy"
			)
	);
echo $vf->createLink($arr,"sample","./sample.php");


●実行結果↓

<script type="text/javascript"> 
<!--
document.write("<a href=\"\" onClick=\"document.postForm_1.submit();return false;\" target=\"_self\">sample</a>\n");
document.write("<form name=\"postForm_1\" method=\"POST\" action=\"./sample.php\">\n");
document.write("<input name=\"name\" type=\"hidden\" value=\"名前\" />\n");
document.write("<input name=\"tokens[access_token]\" type=\"hidden\" value=\"xxxxxxxxxxxxx\" />\n");
document.write("<input name=\"tokens[access_token_secret]\" type=\"hidden\" value=\"yyyyyyyyyyyyy\" />\n");
document.write("</form>\n");
-->
</script>
<noscript>
<form method="POST" action="./sample.php">
<input name="name" type="hidden" value="名前">
<input name="tokens[access_token]" type="hidden" value="xxxxxxxxxxxxx">
<input name="tokens[access_token_secret]" type="hidden" value="yyyyyyyyyyyyy">
<input type="submit" value="sample">
</form>
</noscript>


*/

Class VirtualForm {
	
	function __construct() {
	
		$this->formCnt = 1;
		
	}
	
	//createLink(送信するデータ配列,キャプション,アクション[,メソッド[,ターゲット[,aタグのstyle属性の値[,noscript時のsubmitボタンのstyle属性の値[,シングル/ダブルクオーテーション、半角スペースの置換文字列]]]]])
	function createLink($data,$caption,$action,$method="POST",$target="_self",$linkStyle="",$buttonStyle="",$q_replacement="_") {
	
		if (!is_array($data)) return null;
		
		$this->replacement = $q_replacement;
		$parsedArray = $this->arrayParse($data);
		
		$str = "";
		
		$str .= "<script type=\"text/javascript\">\n";
		$str .= "<!--\n";
		
		if ($linkStyle) $linkStyle = sprintf(" style=\\\"%s\\\"",$linkStyle);
		
		$str .= sprintf("document.write(\"<a href=\\\"\\\" onClick=\\\"document.postForm_%s.submit();return false;\\\" target=\\\"%s\\\"%s>%s</a>\\n\");\n",$this->formCnt,$target,$linkstyle,$caption);
		$str .= sprintf("document.write(\"<form name=\\\"postForm_%s\\\" method=\\\"POST\\\" action=\\\"%s\\\">\\n\");\n",$this->formCnt,$action);
		
		foreach ($parsedArray as $key => $value) $str .= sprintf("document.write(\"<input name=\\\"%s\\\" type=\\\"hidden\\\" value=\\\"%s\\\" />\\n\");\n",$key,$value);
		
		$str .= "document.write(\"</form>\\n\");\n";
		$str .= "-->\n";
		$str .= "</script>\n";
		
		$str .= "<noscript>\n";
		$str .= "<form method=\"{$method}\" action=\"{$action}\">\n";
		
		foreach ($parsedArray as $key => $value) $str .= sprintf("<input name=\"%s\" type=\"hidden\" value=\"%s\">\n",$key,$value);
		
		if ($buttonStyle) $buttonStyle = sprintf(" style=\"%s\"",$buttonStyle);
		
		$str .= sprintf("<input type=\"submit\" value=\"%s\"%s>\n",$caption,$buttonStyle);
		$str .= "</form>\n";
		$str .= "</noscript>\n";
		
		$this->formCnt++;
		
		return $str;
		
	}
	
	private function arrayParse($data) {
		
		$query = http_build_query($data);
		$query = str_replace("&amp;","＆",$query);
		$array = explode("&",$query);
		
		$newArray = array();
		
		foreach ($array as $item) {
		
			$item = preg_replace("/＆/u","&",$item);
			$item = explode("=",$item);
			$newArray[$this->qReplace(urldecode($item[0]))] = $this->qReplace(urldecode($item[1]));
			
		}
		
		return $newArray;
		
	}
	
	private function qReplace($str) {
		
		return preg_replace("/\"|\'|\s/us",$this->replacement,$str);
		
	}

}


?>