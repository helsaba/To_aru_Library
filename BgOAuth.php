<?php

//***********************************************
//************ BgOAuth Version 1.0.1 ************
//***********************************************
//
//　　　　　　　　　　　　　　作者: @To_aru_User
//　　　　　　　　　　　　　　協力: @re4k
//
//バックグラウンドOAuth認証(疑似XAuth認証)ライブラリ
//サーバーによっては正常に動作しないことがあります
//
//
//●使用例
//
// $consumer_key = 'xxxxxxxxxxxxxxx';
// $consumer_secret = 'yyyyyyyyyyyyyy';
// $username = 'hoge';
// $password = 'fuga';
//
// $app = new BgOAuth($consumer_key,$consumer_secret);
// $tokens = $app->getTokens($username,$password);
//
// 成功すると、$tokens['access_token']・$tokens['access_token_secret']でアクセスできます。
// 失敗すると、エラー原因を表す文字列が返されます。
//
//
//●更新履歴
//
// 1.0.1
// ・Private宣言を忘れていたクラス内変数があったので修正
//
//

class BgOAuth {
	
	private $cookie;
	private $user_agent;
	private $consumer_key;
	private $consumer_secret;
	private $oauth_token;
	private $oauth_token_secret;
	private $error;
	
	public function __construct($consumer_key,$consumer_secret) {
			
		$this->user_agent = "Mozilla/5.0 (X11; Linux x86_64; rv:18.0) Gecko/18.0 Firefox/18.0 FirePHP/0.7.1";
		$this->cookie = array();
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		
	}
	
	public function getTokens($username,$password) {
		
		$data = $this->prepare();
		if ($data===false) return $this->error;
		$data['session[username_or_email]'] = $username;
		$data['session[password]'] = $password;
		
		$response = $this->request('https://api.twitter.com/oauth/authorize','POST',$data);
		if ($response===false) {
			$this->error = 'ログイン時、サーバーから応答がありませんでした';
			return $this->error;
		}
		
		$pattern = '@oauth_token=(\w+?)&oauth_verifier=(\w+?)"@';
		if (!preg_match($pattern,$response,$matches)) {
			$this->error = 'oauth_verifierの取得に失敗しました';
			return $this->error;
		}
		if ($matches[1]!=$this->oauth_token) {
			$this->error = 'oauth_tokenが一致しませんでした';
			return $this->error;
		}
		
		$q = $this->getParameters($this->oauth_token,$matches[2],$this->oauth_token_secret,'oauth/access_token');
		$response = $this->request('https://api.twitter.com/oauth/access_token?'.$q,'GET',array());
		if ($response===false) {
			$this->error = 'access_token生成時、サーバーから応答がありませんでした';
			return $this->error;
		}
		
		parse_str($response,$oauth_tokens);
		return array(
			'access_token' => $oauth_tokens['oauth_token'],
			'access_token_secret' => $oauth_tokens['oauth_token_secret']
		);
		
	}
	
	private function prepare() {
		
		$q = $this->getParameters();
		$response = $this->request('https://api.twitter.com/oauth/request_token?'.$q,'GET',array());
		if ($response===false) {
			$this->error = 'リクエストトークン取得時、サーバーから応答がありませんでした';
			return false;
		}
		
		parse_str($response,$request_tokens);
		$this->oauth_token = $request_tokens['oauth_token'];
		$this->oauth_token_secret = $request_tokens['oauth_token_secret'];
		
		$q = 'force_login=true&oauth_token='.$request_tokens['oauth_token'];
		$response = $this->request('https://api.twitter.com/oauth/authorize?'.$q,'GET',array());
		if ($response===false) {
			$this->error = 'ログインページへの遷移時、サーバーから応答がありませんでした';
			return false;
		}
		
		$pattern = '@<input name="authenticity_token" type="hidden" value="(\w+)" />@';
		if (!preg_match($pattern,$response,$matches)) {
			$this->error = 'authenticity_tokenの取得に失敗しました';
			return false;
		}
		
		return array(
			'authenticity_token' => $matches[1],
			'oauth_token' => $this->oauth_token,
			'force_login' => '1'
		);
		
	}
	
	private function request($uri,$type,$data) {
		
		$content = '';
		$_c = array();
		foreach ($this->cookie as $k => $v) $_c[] = $k.'='.$v;
		$context = array(
			'http' => array(
				'header' => implode("\r\n",array(
					'Cookie: '.implode('; ',$_c),
					'User-Agent: '.$this->user_agent
				))
			)
		);
			
		$content = http_build_query($data,'','&');
		$context['http']['method'] = $type;
		if ($type=='POST') {
			$context['http']['header'] = implode("\r\n",array(
				$context['http']['header'],
				'Content-Type: application/x-www-form-urlencoded'
			));
		}
		$context['http']['content'] = $content;
			
		$response = @file_get_contents($uri,false,stream_context_create($context));
		if ($response===false) return false;
		
		foreach ($http_response_header as $r) {
			if (strpos($r,'Set-Cookie')===false) continue;
			$temp = explode(': ',$r);
			$temp = $temp[1];
			$temp = explode(';',$temp);
			$temp = $temp[0];
			list($k, $v) = explode('=',$temp);
			$this->cookie[$k] = $v;
		}
		
		return $response;
		
	}
	
	private function getParameters($token='',$verifier='',$secret='',$call='oauth/request_token') {
		
		$url = 'https://api.twitter.com/'.$call;
		$method = 'GET';
		
		$parameters = array(
			'oauth_consumer_key' => $this->consumer_key,
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_nonce' => md5(microtime().mt_rand()),
			'oauth_version' => '1.0'
		);
		
		if (strlen($token)) $parameters['oauth_token'] = $token;
		if (strlen($verifier)) $parameters['oauth_verifier'] = $verifier;
		
		$params = array_map(array(__CLASS__,'urlencodeRFC3986'),$parameters);
		uksort($params,'strnatcmp');
		
		$pairs = array();
		foreach ($params as $key => $value) {
			$pairs []= $key.'='.$value;
		}
		
		$param = implode('&',$pairs);
		$parts = array($method,$url,$param);
		$parts = array_map(array(__CLASS__,'urlencodeRFC3986'),$parts);
		$body = implode('&',$parts);
		$key_parts = array($this->consumer_secret,$secret);	
		$key_parts = array_map(array(__CLASS__,'urlencodeRFC3986'),$key_parts);
		$key = implode('&',$key_parts);
		$parameters['oauth_signature'] = base64_encode(hash_hmac('sha1',$body,$key,true));
		$params = array_map(array(__CLASS__,'urlencodeRFC3986'),$parameters);
		
		$pairs = array();
		foreach ($params as $key => $value) {
			$pairs []= $key.'='.$value;
		}
		
		$q = implode('&',$pairs);
		return $q;
	
	}
	
	private function urlencodeRFC3986($input) {
		return str_replace('+',' ',str_replace('%7E','~',rawurlencode($input)));
	}
	
}