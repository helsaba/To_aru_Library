#<span style="font-size:150%;">To\_aru\_Library</span>
<dl style="margin-top:30px;">
<dt style="margin:10px;font-size:120%;">これ何ぞ</dt>
<dd>個人的に需要があって作った<b>PHP</b>ライブラリ。主に<b>Twitter</b>向け。<br>
リポジトリの扱いに慣れてないが故に、<s>何回かリセットしちゃったのはご愛嬌ということで</s><br>
<s>READMEを設置できるのもこのファイル書いてる今知った</s>
</dd>
</dl>
# ライブラリ一覧
<p>全部1つのファイルにまとめちゃってもいいんだけどやっぱり分けたほうがいいかなって（適当）</p>

<ul>

<li style="margin-top:20px;">
<span style="font-size:150%"><a href="http://github.com/Certainist/To_aru_Library/blob/master/explodeTweet.php">Explode Tweet</a><img src="http://ishisuke007.yh.land.to/push.png" style="vertical-align:bottom;" height="50"></span>
<dl style="margin-top:15px;">
<dt style="margin:10px;font-size:120%;">概要</dt>
<dd>長いツイートを最大で140字毎に、適当な部分で分割して配列で返す</dd>
<dt style="margin:10px;font-size:120%;">関数の仕様</dt>
<dd>array <b>explodeTweet</b> ( string <i>$str</i> )</dd>
<dt style="margin:10px;font-size:120%;">詳細</dt>
<dd>
ツイート本文を容易に分割することが出来ます。<br>
140字毎にスクリーンネームやURL、英文節を壊さないように区切って分割します。<br>
全てのURLはt.coに短縮されるため、20文字として扱われます。<br>
先頭にリプライヘッダがある場合、分割された全ての本文部分にそれを付加します。
</dd>
</dl>
</li>

<li style="margin-top:20px;">
<span style="font-size:150%">Array Slide<img src="http://ishisuke007.yh.land.to/push.png" style="vertical-align:bottom;" height="50"></span>
<dl style="margin-top:15px;">
<dt style="margin:10px;font-size:120%;">概要</dt>
<dd>配列の要素を指定し、指定した分だけ要素間を移動させる</dd>
<dt style="margin:10px;font-size:120%;">詳細</dt>
<dd>
概要の通りです。配列操作に優れたPHPの関数ですが、この目的に該当する関数が見つからず、<br>
それがどうしても必要な時があり、尚且つ汎用性が高そうなものなので、ライブラリにしてみました。<br>
オプションで、配列の要素の指定方法を、デフォルトの<b>「キー」</b>から<b>「番目」</b>に変更することが出来ます。<br>
<dt style="margin:10px;font-size:120%;">関数の仕様</dt>
<dd>
<dl>
<dt><a href="https://github.com/Certainist/To_aru_Library/blob/master/arraySlide-1.1.php">Version 1.0 系</a></dt>
<dd>
array <b>array_slide</b> ( array $array , mixed <i>$key</i> , int <i>$amount</i> [, bool <i>$search_target_with_order = FALSE</i> ] )<br>
配列を値渡しし、処理された配列を返します。
</dd>
<dt><a href="https://github.com/Certainist/To_aru_Library/blob/master/arraySlide-2.1.php">Version 2.0 系</a></dt>
<dd>
bool <b>array_slide</b> ( array <i>&$array</i> , mixed <i>$key</i> , int <i>$amount</i> [, bool <i>$search_target_with_order = FALSE</i> ] )<br>
配列を<u>参照渡し</u>し、処理の結果を論理値で返します。
</dd>
</dl>
</dd>

</dd>
</dl>
</li>

<li style="margin-top:20px;">
<span style="font-size:150%"><a href="http://github.com/Certainist/To_aru_Library/blob/master/explodeTweet.php">Analyze Entities</a></span>
<dl style="margin-top:15px;">
<dt style="margin:10px;font-size:120%;">概要</dt>
<dd>ツイート文字列にエンティティ情報を適用したものを返す</dd>
<dt style="margin:10px;font-size:120%;">関数の仕様</dt>
<dd>
string <b>analyzeEntities</b> ( string <i>$text</i> , SimpleXMLElement <i>$entities</i> )<br>
</dd>
<dt style="margin:10px;font-size:120%;">詳細</dt>
<dd>
ツイートのエンティティ情報を解析し、それにマッチした部分を指定の文字列に置換して返します<br>
「指定の文字列」については、関数を直接編集してください。
</dd>

<li style="margin-top:20px;">
<span style="font-size:150%"><a href="http://github.com/Certainist/To_aru_Library/blob/master/explodeTweet.php">Virtual Form</a></span>
<dl style="margin-top:15px;">
<dt style="margin:10px;font-size:120%;">概要</dt>
<dd>JavaScriptを使い、aタグ形式でPOST可能なリンクを生成する</dd>
<dt style="margin:10px;font-size:120%;">クラス・関数の仕様</dt>
<dd>
<i>$obj</i> = new <b>VirtualForm</b>;<br>
echo <i>$obj</i>-><b>createLink</b> ( array <i>$data</i> , string <i>$caption</i> , string <i>$action</i> [ , string <i>$method = "POST"</i> [ , string <i>$target = "_self"</i> [ , string <i>$linkStyle</i> [ , string <i>$buttonStyle</i> ]]]] );
</dd>
<dt style="margin:10px;font-size:120%;">詳細</dt>
<dd>
<span style="font-size:120%;"><b><s>正直これ問題多すぎな失敗作で尚且つ実用性微妙なので読まなくていいです</b></s></span><br>
簡単にaタグでPOSTが出来るリンクを張れます。<br>
多次元配列に対応しています。<br>
JavaScriptが使えない場合はSubmitボタンで表示します。<br>
<b>「postForm_1」「postForm_2」「postForm_3」…</b>という風にフォームに名前をつけていくので、<br>
これらと重複するフォームを作らないように注意してください。
</dd>
</dl>
</li>

</ul>

# あとがき
<p>
<s>誰得ライブラリでした</s><br>
explodeTweet関数とarray_slide関数は個人的に超便利だと思うので使っていただけると嬉しいです
</p>