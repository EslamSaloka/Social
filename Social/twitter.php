<?php
namespace App\Social;
class Twitter {

    private $consumerKey;
    private $consumerSecret;
    private $accessToken;
    private $accessTokenSecret;

    function __construct() {
        $this->consumerKey        = config('social.Twitter_consumerKey');
        $this->consumerSecret     = config('social.Twitter_consumerSecret');
        $this->accessToken        = config('social.Twitter_accessToken');
        $this->accessTokenSecret  = config('social.Twitter_accessTokenSecret');
    }

    function RunScript($twusername,$maximum) {
        $authtime     = time();
        $twBase       = "http://www.twitter.com/{$twusername}/status/";
        $url          = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $base         = 'GET&'.rawurlencode($url).'&'.rawurlencode("count={$maximum}&oauth_consumer_key={$this->consumerKey}&oauth_nonce={$authtime}&oauth_signature_method=HMAC-SHA1&oauth_timestamp={$authtime}&oauth_token={$this->accessToken}&oauth_version=1.0&screen_name={$twusername}");
        $key          = rawurlencode($this->consumerSecret).'&'.rawurlencode($this->accessTokenSecret);
        $signature    = rawurlencode(base64_encode(hash_hmac('sha1', $base, $key, true)));
        $oauth_header = "oauth_consumer_key=\"{$this->consumerKey}\", oauth_nonce=\"{$authtime}\", oauth_signature=\"{$signature}\", oauth_signature_method=\"HMAC-SHA1\", oauth_timestamp=\"{$authtime}\", oauth_token=\"{$this->accessToken}\", oauth_version=\"1.0\", ";
        return $this->GetData($oauth_header,$twusername,$maximum,$url);
    }


    function GetData($oauth_header,$twusername,$maximum,$url) {
        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, array("Authorization: Oauth {$oauth_header}", 'Expect:'));
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        curl_setopt($curl_request, CURLOPT_URL, $url."?screen_name={$twusername}&count={$maximum}");
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl_request);
        curl_close($curl_request);
        return json_decode($response, true);
    }
}