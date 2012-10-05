<?

//■Explode Tweet Library Ver1.5■//
//
//ツイートを容易に分割することが出来ます。
//140字毎にURLや英文節を壊さないように区切って分割します。
//全てのURLはt.coに短縮されるため、20文字として扱われます。
//先頭にリプライヘッダがある場合、分割された先頭以外のツイートにもそれを付加します。
//
///Ver1.5
///・全ての文節が収まりきる場合も接尾辞を足すとオーバーしてしまう場合に、
///　次のツイートに持ち越すようになっていたバグを修正。
///　収まりきることが確定した場合、そのまま最後まで文節を付加させるようにした。
///
///Ver1.4.2
///・接頭辞と接尾辞の記述ミスを修正
///
///Ver1.4.1
///・makeNewArray関数を外部利用可能に
///
///Ver1.4
///・改行コードを全て\nに統一して計算するように変更
///・接頭辞と接尾辞を自動で振り分けるように改善
///
///Ver1.3.1
///・Ver1.3.0でのミスを修正
///
///Ver1.3
///・140字以内で即座にカットする際にIDを中途半端にカットして他人を巻き込む事故を回避するように修正
///（mb_strrev関数、cutTweet関数）を追加
///
///Ver1.2.1
///・「ヘッダーが検出されず、かつRTフォーマットが見つかった場合は即座に140字カットして返す」の箇所の、
///　本来「mb_substr」関数を使うべきところに誤って「mb_strlen」関数を用いていたのを修正
///
///Ver1.2
///・名前をExplodeTweetLibraryに変更
///・クラス名も変更
///
///Ver1.1
///・致命的なバグを修正
///・返す配列を1次になるように改良
///・array_splice2を削除（array_spliceで表現可能だった）
//
//
//※arraySplit関数(makeNewArray関数とセットで)、mb_strrev関数、cutTweet関数は外部利用も可能です
//


/*

●例↓

require_once("./explodeTweet.php");

$text = <<<EOD

The U.S. central bank is deciding whether to launch new measures to stimulate the weak U.S. economy.

After a two-day meeting, Federal Reserve officials are set to announce any policy changes at mid-day Thursday, with Chairman Ben Bernanke later making a statement on the Fed's projections for the American economy, the world's largest.

Bernanke last month called the slack hiring in the U.S. labor market a 'grave concern.'

With that in mind, American economists are predicting the central bank will approve a new round of bond-buying to try to decrease already-low interest rates and spur borrowing and spending by businesses and consumers. The Fed has bought more than 2 dollars trillion worth of U.S. Treasury bonds and home loan securities since the world financial crisis in 2008, but the country's economic growth has remained sluggish.

Advertisement:: http://google.com http://yahoo.com http://www.youtube.com http://blogs.voanews.com/breaking-news/2012/09/13/us-central-bank-weighs-new-economic-stimulus/

The central bank could also extend its timetable beyond late 2014 for keeping its benchmark borrowing rate near zero percent.

The central bank maintains political neutrality in the U.S. But whatever policy changes it might adopt would land in the final stages of country's presidential election campaign, where the state of the economy is the key issue.

Republican challenger Mitt Romney says the incumbent Democrat, President Barack Obama, has failed in his oversight of the American economy and that he would not reappoint Bernanke when his Fed chairmanship ends in early 2014. Mr. Obama has pointed to 30 months of job growth in the U.S. and says Mr. Romney would return to policies that led to the worst U.S. economic downturn since the Great Depression of the 1930's.

The U.S. government reported last week that the country's labor market added just 96,000 jobs in August, not near enough to reduce the country's unemployment count of nearly 13 million workers. The jobless rate has been above an unusually high 8 percent level for 43 straight months.

The government said Thursday the number of Americans making first-time claims for jobless benefits last week increased by 15,000 to 382,000, another sign of slow job growth.

EOD;

var_dump(explodeTweet($text));


●実行結果↓


array(18) {
  [0]=>
  string(136) "
The U.S. central bank is deciding whether to launch new measures to stimulate the weak U.S. economy.

After a two-day meeting, (..cont)"
  [1]=>
  string(135) "(cont..)Federal Reserve officials are set to announce any policy changes at mid-day Thursday, with Chairman Ben Bernanke later (..cont)"
  [2]=>
  string(139) "(cont..)making a statement on the Fed's projections for the American economy, the world's largest.

Bernanke last month called the (..cont)"
  [3]=>
  string(140) "(cont..)slack hiring in the U.S. labor market a 'grave concern.'

With that in mind, American economists are predicting the central (..cont)"
  [4]=>
  string(139) "(cont..)bank will approve a new round of bond-buying to try to decrease already-low interest rates and spur borrowing and spending (..cont)"
  [5]=>
  string(136) "(cont..)by businesses and consumers. The Fed has bought more than 2 dollars trillion worth of U.S. Treasury bonds and home loan (..cont)"
  [6]=>
  string(127) "(cont..)securities since the world financial crisis in 2008, but the country's economic growth has remained sluggish.

(..cont)"
  [7]=>
  string(210) "(cont..)Advertisement:: http://google.com http://yahoo.com http://www.youtube.com http://blogs.voanews.com/breaking-news/2012/09/13/us-central-bank-weighs-new-economic-stimulus/

The central bank could (..cont)"
  [8]=>
  string(137) "(cont..)also extend its timetable beyond late 2014 for keeping its benchmark borrowing rate near zero percent.

The central bank (..cont)"
  [9]=>
  string(136) "(cont..)maintains political neutrality in the U.S. But whatever policy changes it might adopt would land in the final stages of (..cont)"
  [10]=>
  string(135) "(cont..)country's presidential election campaign, where the state of the economy is the key issue.

Republican challenger Mitt (..cont)"
  [11]=>
  string(140) "(cont..)Romney says the incumbent Democrat, President Barack Obama, has failed in his oversight of the American economy and that he (..cont)"
  [12]=>
  string(140) "(cont..)would not reappoint Bernanke when his Fed chairmanship ends in early 2014. Mr. Obama has pointed to 30 months of job growth (..cont)"
  [13]=>
  string(134) "(cont..)in the U.S. and says Mr. Romney would return to policies that led to the worst U.S. economic downturn since the Great (..cont)"
  [14]=>
  string(140) "(cont..)Depression of the 1930's.

The U.S. government reported last week that the country's labor market added just 96,000 jobs in (..cont)"
  [15]=>
  string(139) "(cont..)August, not near enough to reduce the country's unemployment count of nearly 13 million workers. The jobless rate has been (..cont)"
  [16]=>
  string(140) "(cont..)above an unusually high 8 percent level for 43 straight months.

The government said Thursday the number of Americans making(..cont)"
  [17]=>
  string(123) "(cont..) first-time claims for jobless benefits last week increased by 15,000 to 382,000, another sign of slow job growth.
"
}

*/

mb_internal_encoding('UTF-8');

//メイン関数
function explodeTweet($str) {
	
	//改行コードを\nに統一
	$str = preg_replace("/\r\n/","\n",$str);
	
	//オブジェクト生成
	$c = new explodeTweetClass($str);
	
	//接頭辞と接尾辞を決定
	if (mb_strlen($str)!=strlen($str))
	return $c->explodeTweet("(続き) "," (続く)");
	else
	return $c->explodeTweet("(cont..)","(..cont)");

}

//クラス
class explodeTweetClass {
	
	//コンストラクタ
	function __construct($str) {
	
		$this->tweet_in = $str;
		define("URL_MAX",20); //t.coのURLの最大文字数
		define("HEAD_MAX",100); //ヘッダーの最大文字数
		
	}
	
	//ツイート分割
	function explodeTweet($prefix="(続き) ",$suffix=" (続く)") {
	
		//接頭辞と接尾辞を10文字までに制限
		$prefLength = mb_strlen($prefix);
		$suffLength = mb_strlen($suffix);
		if ($prefLength>10 || $suffLength>10) return false;
		
		//ヘッダーが検出されず、かつRTフォーマットが見つかった場合はIDを中途半端に残さないように140字以内にカットして返す
		if (!$this->splitHeader() && preg_match("/(QB|[A-Z]T)[\s　]*@[A-Za-z0-9_]{1,15}:/us",$this->tweet_in)) return array(self::cutTweet($this->tweet_in));
		
		//全てのツイートを一次配列化させるための変数
		$whole_tweets = array();
		
		//文節配列作成
		$texts = $this->makeNewArray($this->body);
		$texts = $this->arraySplit($texts);
		
		$parent = 0;
		$child = 0;
		
		//ヘッダーごとに処理
		foreach ($this->headers as $header) {
			
			//文節配列初期化
			$tempTexts = $texts;

			//ヘッダーの長さ
			$headLength = mb_strlen($header);
			
			//ツイートを作成
			$cnt = 0;
			$TweetLength = 0;
			$in_process = false;
			
			while (true) {
			
				if (!$in_process) {
				
					//各child初期処理
					
					//ヘッダー付加
					$Tweet[$parent][$child] = $header;
					$TweetLength = $headLength;
					$in_process = true;
					
					//全て収まることが確定したかどうかを記録するフラグ
					$this->go_in_flag = false;
					
					if ($child>0) {
					
						//先頭以外のchildにはprefixをつける
						$Tweet[$parent][$child] .= $prefix;
						$TweetLength += $prefLength;
						
					}
					
				} else {
				
					//各child途中処理
					
					//文節が無い場合は脱出
					if ($tempTexts[$cnt]===NULL) break;

					$tempLength = ($tempTexts[$cnt]["type"]=="url") ? URL_MAX : mb_strlen($tempTexts[$cnt]["str"]);
					
					if ($TweetLength + $tempLength <= 140) {
					
						//既成の本文＋現文節の長さが140字以内の場合
						
						if ($TweetLength + $tempLength + $suffLength <= 140) {
						
							//更にsuffixを付けても140字以内の場合
							
							//現文節を追加
							$Tweet[$parent][$child] .= $tempTexts[$cnt]["str"];
							$TweetLength += $tempLength;
							
							//次文節に進む
							$cnt++;
							
						} elseif ($this->go_in($tempTexts,$cnt,$TweetLength)) {
						
							//suffixを付けると140字を超えるが、収まりきることが確定している場合
							
							//現文節を追加
							$Tweet[$parent][$child] .= $tempTexts[$cnt]["str"];
							$TweetLength += $tempLength;
							
							//次文節に進む
							$cnt++;
							
						} else {
						
							//次文節があり、suffixを付けると140字を超える場合
							
							if ($tempTexts[$cnt+1]["type"]=="url") {
							
								//次文節がURLの場合はsuffixを付加
								$Tweet[$parent][$child] .= $suffix;
								
								//次childに進む
								$child++;
								$in_process = false;
								
							} else {
							
								//次々文節の有無に応じて、次文節が次child送りにした後140字以内に収まるか判定
								
								if ($tempTexts[$cnt+2]===NULL && $headLength + $prefLength + mb_strlen($tempTexts[$cnt+1]["str"]) <= 140 || 
								    $headLength + $prefLength + mb_strlen($tempTexts[$cnt+1]["str"]) + $suffLength <= 140) {
								    	
									//収まる場合
									
									//suffixを付加
									$Tweet[$parent][$child] .= $suffix;
									
									//次childに進む
									$child++;
									$in_process = false;
									
								} else {
								
									//収まらない場合
									
									//現文節を140字以内に収まる分だけカットして追加
									$restLength = 140 - $TweetLength - $suffLength;
									$Tweet[$parent][$child] .= mb_substr($tempTexts[$cnt]["str"],0,$restLength).$suffix;
									
									//収まらなかった分を文節配列に挿入
									array_splice($tempTexts,$cnt+1,0,array(array("str"=>mb_substr($tempTexts[$cnt]["str"],$restLength),"type"=>false)));
									
									//次childに進む
									$child++;
									$in_process = false;
									
									//次文節に進む
									$cnt++;
									
								}
								
							}
							
						}
						
					} else {
						
						//既成の本文＋現文節の長さが140字を超過する場合
						
						//現文節を140字以内に収まる分だけカットして追加
						$restLength = 140 - $TweetLength - $suffLength;
						$Tweet[$parent][$child] .= mb_substr($tempTexts[$cnt]["str"],0,$restLength).$suffix;
						
						//収まらなかった分を文節配列に挿入
						array_splice($tempTexts,$cnt+1,0,array(array("str"=>mb_substr($tempTexts[$cnt]["str"],$restLength),"type"=>false)));
						
						//次childに進む
						$child++;
						$in_process = false;
						
						//次文節に進む
						$cnt++;
						
					}
					
				}
			
			}
			
			//完成したツイートを1次配列化して集める
			array_splice($whole_tweets,-1,0,$Tweet[$parent]);
			
			//次parentに進む
			$parent++;
			$child = 0;
		
		}
		
		//まとめたものを返す
		return $whole_tweets;
		
	}
	
	//文字列を反転
	function mb_strrev($str) {
	
		 preg_match_all('/./us',$str,$arr);
		 return implode(array_reverse($arr[0]));
		 
	}
	
	//IDを中途半端に残さないように140字以内にカットして返す
	function cutTweet($tweet) {
		
		if (mb_strlen($tweet)<=140) return $tweet;
		$rev_pattern = "/^[A-Za-z0-9_]{1,15}@/us";
		$rev_str = self::mb_strrev(mb_substr($tweet,0,140));
		while (true) {
			$rev_str = preg_replace($rev_pattern,"",$rev_str,1,$count);
			if ($count<1) return self::mb_strrev($rev_str);
		}
	
	}
	
	//文字列から初期化された配列を作成
	function makeNewArray($str) {
	
		$array[0]["str"] = $str;
		$array[0]["type"] = false;
		return $array;
	
	}
	
	//配列に分割
	function arraySplit($convertedArray) {
	
		/*URLで分割*/
		
		//gTLD
		$gTLD = array("aero","arpa","asia","biz","cat","com","coop","edu","gov","info","int","jobs","mil","mobi","museum","name","net","org","pro","tel","travel");
		
		//ccTLD
		$ccTLD = array("ac","ad","ae","af","ag","ai","al","am","an","ao","aq","ar","as","at","au","aw","ax","az","ba","bb","bd","be","bf","bg","bh","bi","bj","bm",
				"bn","bo","br","bs","bt","bv","bw","by","bz","ca","cc","cd","cf","cg","ch","ci","ck","cl","cm","cn","co","cr","cs","cu","cv","cx","cy","cz",
				"dd","de","dj","dk","dm","do","dz","ec","ee","eg","eh","er","es","et","eu","fi","fj","fk","fm","fo","fr","ga","gb","gd","ge","gf","gg","gh",
				"gi","gl","gm","gn","gp","gq","gr","gs","gt","gu","gw","gy","hk","hm","hn","hr","ht","hu","id","ie","il","im","in","io","iq","ir","is","it",
				"je","jm","jo","jp","ke","kg","kh","ki","km","kn","kp","kr","kw","ky","kz","la","lb","lc","li","lk","lr","ls","lt","lu","lv","ly","ma","mc",
				"md","me","mg","mh","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nc","ne","nf","ng","ni","nl","no",
				"np","nr","nu","nz","om","pa","pf","pg","ph","pk","pl","pm","pn","pr","ps","pt","pw","py","qa","re","ro","rs","ru","rw","sa","sb","sc","sd",
				"se","sg","sh","si","sj","sk","sl","sm","sn","so","sr","st","su","sv","sy","sz","tc","td","tf","tg","th","tj","tk","tl","tm","tn","to","tp",
				"tr","tt","tv","tw","tz","ua","ug","uk","um","us","uy","uz","va","vc","ve","vg","vi","vn","vu","wf","ws","ye","yt","yu","za","zm","zr","zw");
		
		//パターン結合
		$pattern1 = "(https?|ftp):\/\/([A-Za-z0-9]+\.)+(".implode("|",array_merge($gTLD,$ccTLD)).")(\/[^<>\"(){}\\^[\]`。、，”□△◎☆！？～＠\s　]*)?";
		$pattern2 = "([A-Za-z0-9]+\.)+(".implode("|",$gTLD).")(\/[^<>\"(){}\\^[\]`。、，”□△◎☆！？～＠\s　]*)?";
		$pattern3 = "([A-Za-z0-9]+\.){2,}(".implode("|",$ccTLD).")(\/[^<>\"(){}\\^[\]`。、，”□△◎☆！？～＠\s　]*)?";
		$pattern = "/({$pattern1})|({$pattern2})|({$pattern3})/us";
		
		//URLを見つけて配列に区切って入れていく
		while (true) {
		
			$temp_arr = array_pop($convertedArray);
			
			if (preg_match($pattern,$temp_arr["str"],$matches)) {
			
				$url_pos = mb_strpos($temp_arr["str"],$matches[0]);
				$url_len = mb_strlen($matches[0]);
				$url_str = $matches[0];
				$convertedArray[] = array("str"=>mb_substr($temp_arr["str"],0,$url_pos),"type"=>false);
				$convertedArray[] = array("str"=>$url_str,"type"=>"url");
				$convertedArray[] = array("str"=>mb_substr($temp_arr["str"],$url_pos+$url_len),"type"=>false);
				continue;
				
			}
			
			$convertedArray[] = $temp_arr;
			break;
			
		}
		
		//前後関係からURLを再判定
		$last_char = "";
		
		foreach ($convertedArray as &$array) {
		
			if ($array["type"]!="url") {
			
				$last_char = mb_substr($array["str"],mb_strlen($array["str"])-1);
				
			} else {
			
				if (preg_match("/[A-Za-z0-9]/u",$last_char)) $array["type"] = false;
				$last_char = "";
				
			}
		}
		
		/*ハッシュタグ・英単語で分割*/
		
		//パターン
		$pattern = "/@[A-Za-z0-9_]{1,15}|[#♯][ー゛゜々ヾヽぁ-ヶ一-龠ａ-ｚＡ-Ｚ０-９a-zA-Z0-9_]{1,139}|[A-Za-z0-9\-_.,:;]{1,140}/us";
		
		
		//実際に分割
		$cnt=0;
		
		while (true) {
		
			$temp_arr = $convertedArray[$cnt];
			
			if ($temp_arr===NULL) break;
			
			//文節は無視
			if ($temp_arr["type"]!==false) {
			
				$cnt++;
				continue;
				
			}
			
			if (preg_match($pattern,$temp_arr["str"],$matches)) {
			
				$pos = mb_strpos($temp_arr["str"],$matches[0]);
				$len = mb_strlen($matches[0]);
				$str = $matches[0];
			
				array_splice($convertedArray,$cnt,1,
					array(
						array("str"=>mb_substr($temp_arr["str"],0,$pos),"type"=>false),
						array("str"=>$str,"type"=>"other"),
						array("str"=>mb_substr($temp_arr["str"],$pos+$len),"type"=>false)
					)
				);
				$cnt += 2;
				continue;
				
			}
			
			$cnt++;
				
		}
		
		//配列を返す
		return $convertedArray;
		
	}
	
	//ヘッダーと本文を分割
	private function splitHeader() {
		
		if (!preg_match("/^([\s　]*\.?[\s　]*)((@[A-Za-z0-9_]{1,15}[\s　]?)+)(.+)*/us",$this->tweet_in,$first_matches)) {
		
			//ヘッダーが見つからなければヘッダーと本文を初期化してfalseを返す
			$this->headers[] = "";
			$this->body = $this->tweet_in;
			return false;
		
		} else {
			
			//ヘッダーが見つかれば本文を初期化、ヘッダーの1つ1つを取り出して次に進む
			preg_match_all("/@[A-Za-z0-9_]{1,15}/us",$first_matches[2],$second_matches);
			$headChar = $first_matches[1];
			$headScreenNames = $second_matches[0];
			$this->body = $first_matches[4];
			
		}
		
		$in_process = false;
		$prev = false;
		
		//制限字数内でヘッダーを連結していく
		for ($cnt=0;;$cnt++) {
		
			if (!$in_process) {
				$temp = $headChar;
				$in_process = true;
			}
			
			$temp .= $headScreenNames[$cnt]." ";

			if (mb_strlen($temp) > HEAD_MAX) {
			
				$this->headers[] = $prev;
				$temp = "";
				$in_process = false;
				$cnt--;
				
			} elseif (!$headScreenNames[$cnt+1]) {
			
				$this->headers[] = $temp;
				break;
				
			}
			
			$prev = $temp;
			
		}
		
		//trueを返す
		return true;
	
	}
	
	//残りの文節の文字が収まりきるかどうかを判断
	private function go_in($texts,$currentCnt,$currentLength) {
		
		//フラグが既に立っている場合はすぐtrueを返す
		if ($this->go_in_flag==true) return true;
		
		//残っている文字数を計算
		$count = count($texts);
		$sum = 0;
		for ($cnt=$currentCnt;$cnt<$count;$cnt++) {
			$sum += ($texts[$cnt]['type']=='url') ? URL_MAX : mb_strlen($texts[$cnt]['str']);
		}
		
		//140字以内に収まりきる場合はフラグを立て、trueを返す
		if ($sum+$currentLength<=140) {
			$this->go_in_flag==true;
			return true;
		}
		
		//それ以外はfalseを返す
		return false;
		
	}
	
}


?>