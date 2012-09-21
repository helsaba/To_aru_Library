<?php

//■Analyze Entities Library Ver1.1■//
//
//ツイートのエンティティ情報をもとに置換したりリンクを振ったり出来ます。
//「この行を変更」とコメントされた行をご自身の開発されているアプリケーションに合わせて設定してください。
//(いちいちeval関数を使ってると処理が重くなるので)
//
//・Ver1.1
//media_urlを展開するように修正
//
function analyzeEntities($text,$entities) {
	
	
	/* ["@attributes"]の["start"]の値を基準にソートする */
	
	if ($entities->user_mentions->user_mention) 
	
	foreach ($entities->user_mentions->user_mention as $user_mention) {
	
		//ソート用配列
		$sort_arr[] = (int)$user_mention->attributes()->start;
		
		//typeエレメントを設定
		$user_mention->type = "user_mention";
		
		//ソート先配列
		$new_entities[] = $user_mention;
		
	}
	
	if ($entities->urls->url) 
	
	foreach ($entities->urls->url as $url) {
	
		//ソート用配列
		$sort_arr[] = (int)$url->attributes()->start;
		
		//typeエレメントを設定
		$url->type = "url";
		
		//ソート先配列
		$new_entities[] = $url;
		
	}
	
	if ($entities->hashtags->hashtag) 
	
	foreach ($entities->hashtags->hashtag as $hashtag) {
	
		//ソート用配列
		$sort_arr[] = (int)$hashtag->attributes()->start;
		
		//typeエレメントを設定
		$hashtag->type = "hashtag";
		
		//ソート先配列
		$new_entities[] = $hashtag;
		
	}
	
	if ($entities->media->creative) 
	
	foreach ($entities->media->creative as $creative) {
	
		//ソート用配列
		$sort_arr[] = (int)$creative->attributes()->start;
		
		//typeエレメントを設定
		$hashtag->type = "creative";
		
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
		switch ($entity->type) {
		
			case "user_mention":
			
				/* メンションを置換するフォーマット */
				
				$replace_str = "<a href=\"person.php?guid=ON&person={$entity->screen_name}\">@{$entity->screen_name}</a>"; //この行を変更
				
				$text = mb_substr($text,0,$pos+$pos_lag).$replace_str.mb_substr($text,$pos+$pos_lag+$len);
				$pos_lag += mb_strlen($replace_str) - $len;
				break;
				
			case "url":
			
				/* URLを置換するフォーマット */
				
				$replace_str = "<a href=\"{$entity->expanded_url}\">{$entity->expanded_url}</a>"; //この行を変更
				
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