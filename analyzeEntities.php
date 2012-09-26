<?php

//■Analyze Entities Library Ver3.1.1■//
//
//ツイートのエンティティ情報をもとに置換したりリンクを振ったり出来ます。
//replace_str変数をご自身の開発されているアプリケーションに合わせて設定してください。
//(いちいちeval関数を使ってると処理が重くなるので)
//
//
//※ライブラリというより自分用化してきた気もしますが、非常に便利なのでｒｙ
//
//
//・Ver3.1.1
//media_urlのバグを修正
//
//
//・Ver3.1
//media_urlを修正
//ツイートURL処理と改行変換処理を移入
//
//・Ver3.0
//重複しそうな関数が増えてきたのでクラス化
//Vガラケー向けにサムネイル展開を徹底強化
//(ファイルシーク連携)
//
//・Ver2.1
//Ver2.1の不具合を修正
//置換の方法を間違えていた
//
//・Ver2.0
//別に分けていたライブラリ組み込み
//Ver1.1の不具合を修正
//
//・Ver1.1
//media_urlを展開するように修正
//

function analyzeEntities($text,$entities) {

	return analyzeEntitiesClass::analyze($text,$entities);

}

class analyzeEntitiesClass {
	
	//UID取得
	function getUID() {

		//DoCoMo
		if (isset($_SERVER['HTTP_X_DCMGUID'])) {
			$mobile_id = $_SERVER['HTTP_X_DCMGUID'];
		}
		//au
		else if (isset($_SERVER['HTTP_X_UP_SUBNO'])) {
			$mobile_id = $_SERVER['HTTP_X_UP_SUBNO'];
		}
		//SoftBank
		else if (isset($_SERVER['HTTP_X_JPHONE_UID'])) {
			$mobile_id = $_SERVER['HTTP_X_JPHONE_UID'];
		}
		//PC
		else {
			return null;
		}
		
		return $mobile_id;
		
	}
	
	//解析
	function analyze($text,$entities) {
		
		/* ["@attributes"]の["start"]の値を基準にソートする */
		
		if ($entities->user_mentions->user_mention) 
		
		foreach ($entities->user_mentions->user_mention as $user_mention) {
		
			//ソート用配列
			$sort_arr[] = (int)$user_mention->attributes()->start;
			
			//typeエレメントを設定
			$user_mention->analyzed_type = "user_mention";
			
			//ソート先配列
			$new_entities[] = $user_mention;
			
		}
		
		if ($entities->urls->url) 
		
		foreach ($entities->urls->url as $url) {
		
			//ソート用配列
			$sort_arr[] = (int)$url->attributes()->start;
			
			//typeエレメントを設定
			$url->analyzed_type = "url";
			
			//ソート先配列
			$new_entities[] = $url;
			
		}
		
		if ($entities->hashtags->hashtag) 
		
		foreach ($entities->hashtags->hashtag as $hashtag) {
		
			//ソート用配列
			$sort_arr[] = (int)$hashtag->attributes()->start;
			
			//typeエレメントを設定
			$hashtag->analyzed_type = "hashtag";
			
			//ソート先配列
			$new_entities[] = $hashtag;
			
		}
		
		if ($entities->media->creative) 
		
		foreach ($entities->media->creative as $creative) {
		
			//ソート用配列
			$sort_arr[] = (int)$creative->attributes()->start;
			
			//typeエレメントを設定
			$creative->analyzed_type = "creative";
			
			//ソート先配列
			$new_entities[] = $creative;
			
		}
		
		//改行コードを<br>に変換
		$text = preg_replace("/\n/","<br>",$text);
		
		//エンティティ情報が空の場合はそのままテキストを返す
		if (!$new_entities) return $text;
		
		//ソート処理
		array_multisort($sort_arr,$new_entities);
		
		
		/* 実際に置換していく */
		
		//1回置換するごとに起こる$posのズレに配慮(この計算を容易にするためにソートした)
		$pos_lag = 0; 
		
		foreach ($new_entities as $entity) {
		
			$pos = $entity->attributes()->start;
			$len = $entity->attributes()->end - $pos;
			
			//typeエレメントで場合分け
			switch ($entity->analyzed_type) {
			
				case "user_mention":
				
					/* メンションを置換するフォーマット */
					
				$replace_str = "<a href=\"person.php?guid=ON&person={$entity->screen_name}\">@{$entity->screen_name}</a>"; //この行を変更
					
				$text = mb_substr($text,0,$pos+$pos_lag).$replace_str.mb_substr($text,$pos+$pos_lag+$len);
					$pos_lag += mb_strlen($replace_str) - $len;
					break;
					
				case "url":
				
					/* URLを置換するフォーマット */
					
					//////変更ブロックここから
					$details = self::get_image_details($entity->expanded_url);
					if (!is_null($details['youtube'])) 
					$replace_str = "<br><a href=\"{$details['youtube']}\"><img src=\"{$details['thumb']}\"></a><br>";
					elseif (!is_null($details['thumb']) && !is_null(self::getUID()))
					$replace_str = "<br><a href=\"http://web.fileseek.net/getimg.cgi?guid=ON&u=".rawurlencode($details['raw'])."\"><img src=\"{$details['thumb']}\"></a><br>";
					elseif (!is_null($details['thumb']))
					$replace_str = "<br><a href=\"{$entity->expanded_url}\"><img src=\"{$details['thumb']}\"></a><br>";
					elseif (preg_match("#http://twitter\.com/([A-Za-z0-9_]{1,15})/status(es)?/(\d+)/?#",$entity->expanded_url))
					$replace_str = "<a href=\"showTweet.php?guid=ON&id={$matches[3]}\" style=\"color:blue;\">{$matches[1]}のツイート</a>";
					else
					$replace_str = "<a href=\"{$entity->expanded_url}\">{$entity->display_url}</a>";
					//////変更ブロックここまで
					
					$text = mb_substr($text,0,$pos+$pos_lag).$replace_str.mb_substr($text,$pos+$pos_lag+$len);
					$pos_lag += mb_strlen($replace_str) - $len;
					break;
					
				case "hashtag":
				
					/* ハッシュタグを置換するフォーマット */
					
					$replace_str = "<a href=\"\">#{$entity->text}</a>"; //この行を変更
					
				$text = mb_substr($text,0,$pos+$pos_lag).$replace_str.mb_substr($text,$pos+$pos_lag+$len);
					$pos_lag += mb_strlen($replace_str) - $len;
					break;
					
				case "creative":
				
					/* クリエイティブ(画像等)を置換するフォーマット */
					
					//////変更ブロックここから
					if (is_null(self::getUID())) $raw_url = $entity->media_url;
					else $raw_url = "http://web.fileseek.net/getimg.cgi?guid=ON&u=".rawurlencode($entity->media_url);
					$replace_str = "<br><a href=\"{$raw_url}\"><img src=\"{$entity->media_url}:thumb\"></a><br>";
					//////変更ブロックここまで
					
					$text = mb_substr($text,0,$pos+$pos_lag).$replace_str.mb_substr($text,$pos+$pos_lag+$len);
					$pos_lag += mb_strlen($replace_str) - $len;
					break;
					
			}
			
		}
		
		//処理したテキストを返す
		return $text;
		
	}
	
	//画像情報を取得
	function get_image_details($url) {
		
		// twitpic
		if (preg_match('/http:\/\/twitpic[.]com\/(\w+)/',$url,$matches)) {
			$img_thumb = 'http://twitpic.com/show/mini/'.$matches[1];
			$img_raw = 'http://twitpic.com/show/full/'.$matches[1];
		
		// Mobypicture
		} elseif (preg_match('/http:\/\/moby[.]to\/(\w+)/',$url,$matches)) {
			$img_thumb = 'http://moby.to/'.$matches[1].':thumbnail';
			$img_raw = 'http://moby.to/'.$matches[1].':medium';
		
		// yFrog
		} elseif (preg_match('/http:\/\/yfrog[.]com\/(\w+)/',$url,$matches)) {
			$img_thumb = 'http://yfrog.com/'.$matches[1].':small';
			$img_raw = 'http://yfrog.com/'.$matches[1].':medium';
		
		// 携帯百景
		} elseif (preg_match('/http:\/\/movapic[.]com\/pic\/(\w+)/',$url,$matches)) {
			$img_thumb = 'http://image.movapic.com/pic/t_'.$matches[1].'.jpeg';
			$img_raw = 'http://image.movapic.com/pic/s_'.$matches[1].'.jpeg';
		
		// はてなフォトライフ
		} elseif (preg_match('/http:\/\/f[.]hatena[.]ne[.]jp\/(([\w\-])[\w\-]+)\/((\d{8})\d+)/',$url,$matches)) {
			$img_thumb = 'http://img.f.hatena.ne.jp/images/fotolife/'.$matches[2].'/'.$matches[1].'/'.$matches[4].'/'.$matches[3].'_m.jpg';
			$img_raw = 'http://img.f.hatena.ne.jp/images/fotolife/'.$matches[2].'/'.$matches[1].'/'.$matches[4].'/'.$matches[3].'.jpg';
		
		// PhotoShare1
		} elseif (preg_match('/http:\/\/(?:www[.])?bcphotoshare[.]com\/photos\/\d+\/(\d+)/',$url,$matches)) {
			$img_thumb = 'http://images.bcphotoshare.com/storages/'.$matches[1].'/thumb68.jpg';
			$img_raw = 'http://images.bcphotoshare.com/storages/'.$matches[1].'/large.jpg';
		
		// PhotoShare2
		} elseif (preg_match('/http:\/\/bctiny[.]com\/p(\w+)/',$url,$matches)) {
			
			$img_thumb = 'http://images.bcphotoshare.com/storages/'.base_convert($matches[1],36,10).'/thumb68.jpg';
			$img_raw = 'http://images.bcphotoshare.com/storages/'.base_convert($matches[1],36,10).'/large.jpg';
		
		// img.ly
		} elseif (preg_match('/http:\/\/img[.]ly\/(\w+)/',$url,$matches)) {
			
			$img_thumb = 'http://img.ly/show/thumb/'.$matches[1];
			$img_raw = 'http://img.ly/show/full/'.$matches[1];
		
		// Twitgoo
		} elseif (preg_match('/http:\/\/twitgoo[.]com\/(\w+)/',$url,$matches)) {
		
			$img_thumb = 'http://twitgoo.com/'.$matches[1].'/mini';
			$img_raw = 'http://twitgoo.com/'.$matches[1].'/img';
		
		// youtube
		} elseif (preg_match('/http:\/\/(?:www[.]youtube[.]com\/watch(?:\?|#!)v=|youtu[.]be\/)([\w\-]+)(?:[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]*)/',$url,$matches)) {
			
			$img_thumb = 'http://i.ytimg.com/vi/'.$matches[1].'/default.jpg';
			$img_raw = 'http://i.ytimg.com/vi/'.$matches[1].'/hqdefault.jpg';
			$youtube = 'http://youtu.be/'.$matches[1];
		
		// imgur
		} elseif (preg_match('/http:\/\/imgur[.]com\/(\w+)/',$url,$matches)) {
		
			$img_thumb = 'http://i.imgur.com/'.$matches[1].'s.jpg';
			$img_raw = 'http://i.imgur.com/'.$matches[1].'.jpg';
			
		// TweetPhoto, Plixi, Lockerz
		} elseif (preg_match('/http:\/\/tweetphoto[.]com\/\d+|http:\/\/plixi[.]com\/p\/\d+|http:\/\/lockerz[.]com\/s\/\d+/',$url,$matches)) {
		
			$img_thumb = 'http://api.plixi.com/api/TPAPI.svc/imagefromurl?size=thumbnail&url='.$matches[0];
			$img_raw = 'http://api.plixi.com/api/TPAPI.svc/imagefromurl?size=big&url='.$matches[0];
			
		// Ow.ly
		} elseif (preg_match('/http:\/\/ow[.]ly\/i\/(\w+)/',$url,$matches)) {
		
			$img_thumb = 'http://static.ow.ly/photos/thumb/'.$matches[1].'.jpg';
			$ow_jpg = 'http://static.ow.ly/photos/original/'.$matches[1].'.jpg';
			$ow_png = 'http://static.ow.ly/photos/original/'.$matches[1].'.png';
			$ow_gif = 'http://static.ow.ly/photos/original/'.$matches[1].'.gif';
			
			function ow_exist($ow_url) {
				$ow_get = @simplexml_load_string(@file_get_contents($ow_url));
				if ($ow_jpg===false) return true; else return false;
			}
			
			if (ow_exist($ow_jpg)) $img_raw = $ow_jpg;
			elseif (ow_exist($ow_png)) $img_raw = $ow_png;
			elseif (ow_exist($ow_gif)) $img_raw = $ow_gif;
			else $img_raw = $img_thumb;
		
		// Instagram
		} elseif (preg_match('/http:\/\/instagr[.]am\/p\/([\w\-]+)\//',$url,$matches)) {
			
			$img_thumb = 'http://instagr.am/p/'.$matches[1].'/media/?size=t';
			$img_raw = 'http://instagr.am/p/'.$matches[1].'/media/?size=l';
			
		// フォト蔵
		} elseif (preg_match('/http:\/\/photozou[.]jp\/photo\/show\/\d+\/([\d]+)/',$url,$matches)) {
		
			$img_thumb = 'http://photozou.jp/p/thumb/'.$matches[1];
			$img_raw = 'http://photozou.jp/p/img/'.$matches[1];
			
		// ついっぷる フォト
		} elseif (preg_match('/http:\/\/p[.]twipple[.]jp\/([\w]+)/',$url,$matches)) {
			
			$img_thumb = 'http://p.twpl.jp/show/thumb/'.$matches[1];
			$img_raw = 'http://p.twpl.jp/show/large/'.$matches[1];
		
		//その他直接表示の画像
		} elseif (preg_match('/http:\/\/.+?\.(jpg|png|gif|jpeg)/ui',$url,$matches)) {
			
			$img_thumb = 'http://i.tinysrc.mobi/80/'.$matches[0];
			$img_raw = $matches[0];
			
		}
		
		return array('thumb'=>$img_thumb,'raw'=>$img_raw,'youtube'=>$youtube);
		
	}

}

?>