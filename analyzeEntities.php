<?php

//■Analyze Entities Library Ver1.1■//
//
//ツイートのエンティティ情報をもとに置換したりリンクを振ったり出来ます。
//replace_str変数をご自身の開発されているアプリケーションに合わせて設定してください。
//(いちいちeval関数を使ってると処理が重くなるので)
//
//・Ver2.0
//別に分けていたライブラリ組み込み
//Ver1.1の不具合を修正
//
//・Ver1.1
//media_urlを展開するように修正
//

function analyzeEntities($text,$entities) {
	
	//URLパターン
	$url_patterns = array(
		
		// twitpic
		array('/http:\/\/twitpic[.]com\/(\w+)/', '<img src="http://twitpic.com/show/thumb/$1" width="150" height="150" />'),
		
		// Mobypicture
		array('/http:\/\/moby[.]to\/(\w+)/', '<img src="http://moby.to/$1:small" />'),
		
		// yFrog
		array('/http:\/\/yfrog[.]com\/(\w+)/', '<img src="http://yfrog.com/$1:small" />'),
		
		// 携帯百景
		array('/http:\/\/movapic[.]com\/pic\/(\w+)/', '<img src="http://image.movapic.com/pic/s_$1.jpeg" />'),
		
		// はてなフォトライフ
		array('/http:\/\/f[.]hatena[.]ne[.]jp\/(([\w\-])[\w\-]+)\/((\d{8})\d+)/', '<img src="http://img.f.hatena.ne.jp/images/fotolife/$2/$1/$4/$3_120.jpg" />'),
		
		// PhotoShare1
		array('/http:\/\/(?:www[.])?bcphotoshare[.]com\/photos\/\d+\/(\d+)/', '<img src="http://images.bcphotoshare.com/storages/$1/thumb180.jpg" width="180" height="180" />'),
		
		// PhotoShare2
		array('/http:\/\/bctiny[.]com\/p(\w+)/e', '\'<img src="http://images.bcphotoshare.com/storages/\' . base_convert("$1", 36, 10) . \'/thumb180.jpg" width="180" height="180" />\''),
		
		// img.ly
		array('/http:\/\/img[.]ly\/(\w+)/', '<img src="http://img.ly/show/thumb/$1" width="150" height="150" />'),
		
		// brightkite
		array('/http:\/\/brightkite[.]com\/objects\/((\w{2})(\w{2})\w+)/', '<img src="http://cdn.brightkite.com/$2/$3/$1-feed.jpg" />'),
		
		// Twitgoo
		array('/http:\/\/twitgoo[.]com\/(\w+)/', '<img src="http://twitgoo.com/$1/mini" />'),
		
		// pic.im
		array('/http:\/\/pic[.]im\/(\w+)/', '<img src="http://pic.im/website/thumbnail/$1" />'),
		
		// youtube
		array('/http:\/\/(?:www[.]youtube[.]com\/watch(?:\?|#!)v=|youtu[.]be\/)([\w\-]+)(?:[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]*)/', '<img src="http://i.ytimg.com/vi/$1/hqdefault.jpg" width="240" height="180" />'),
		
		// imgur
		array('/http:\/\/imgur[.]com\/(\w+)[.]jpg/', '<img src="http://i.imgur.com/$1l.jpg" />'),
		
		// TweetPhoto, Plixi, Lockerz
		array('/http:\/\/tweetphoto[.]com\/\d+|http:\/\/plixi[.]com\/p\/\d+|http:\/\/lockerz[.]com\/s\/\d+/', '<img src="http://api.plixi.com/api/TPAPI.svc/imagefromurl?size=mobile&url=$0" />'),
		
		// Ow.ly
		array('/http:\/\/ow[.]ly\/i\/(\w+)/', '<img src="http://static.ow.ly/photos/thumb/$1.jpg" width="100" height="100" />'),
		
		// Instagram
		array('/http:\/\/instagr[.]am\/p\/([\w\-]+)\//', '<img src="http://instagr.am/p/$1/media/?size=t" width="150" height="150" />'),
		
		// フォト蔵
		array('/http:\/\/photozou[.]jp\/photo\/show\/\d+\/([\d]+)/', '<img src="http://photozou.jp/p/thumb/$1" />'),
		
		// ついっぷる フォト
		array('/http:\/\/p[.]twipple[.]jp\/([\w]+)/', '<img src="http://p.twipple.jp/show/thumb/$1" />'),
		
		//その他直接表示の画像
		array('/(http:\/\/.+?\.(jpg|png|gif|jpeg))/ui', '<img src="http://i.tinysrc.mobi/$1" />')
		
	);
	
	
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
				
				//このブロックを変更
				$matched = false;
				foreach ($url_patterns as $pattern) {
					if (preg_match($pattern[0],$entity->expanded_url)) {
						$replace_str = "<a href=\"{$entity->expanded_url}\">$pattern[1]</a>";
						$matched = true;
						break;
					}
				}
				if (!$matched) $replace_str = "<a href=\"{$entity->expanded_url}\">{$entity->display_url}</a>";
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
				
				$replace_str = "<a href=\"{$entitiy->media_url}:medium\"><img src=\"{$entity->media_url}:thumb\"></a>"; //この行を変更
				
				$text = mb_substr($text,0,$pos+$pos_lag).$replace_str.mb_substr($text,$pos+$pos_lag+$len);
				$pos_lag += mb_strlen($replace_str) - $len;
				break;
				
		}
		
	}
	
	//処理したテキストを返す
	return $text;
	
}

?>