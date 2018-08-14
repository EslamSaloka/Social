<?php
namespace App\Social;
class Facebook {

    private $appID;
    private $appSecret;

    function __construct() {
        $this->appID      = config('social.appID');
        $this->appSecret  = config('social.appSecret');
    }

    function RunScript($fbusername,$fbuserid) {
        $maximum = 10;
        $authentication = $this->file_get_contents_curl("https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id={$this->appID}&client_secret={$this->appSecret}");
        $fbauth = json_decode($authentication,true);
        $fbresponse = $this->file_get_contents_curl("https://graph.facebook.com/{$fbuserid}/feed?access_token={$fbauth['access_token']}&token_type={$fbauth['token_type']}&limit={$maximum}");

        $posts = json_decode($fbresponse, true);
        
        //get profile picture
        $fbpicJ = $this->file_get_contents_curl("https://graph.facebook.com/{$fbuserid}/picture?access_token={$fbauth['access_token']}&token_type={$fbauth['token_type']}&redirect=false");
        
        $fbpic = json_decode($fbpicJ,true);

        if (isset($fbpic['data'])) {
            $fbpicurl = $fbpic['data']['url'];
        } else {
            $fbpicurl = '';
        }

        $fbposts = array();
        
        if (isset($posts['data'])) {

            $iii = 0;

            foreach($posts['data'] as $fbpost) {
                if (isset($fbpost['message'])) {
                    if ($iii<3) {
                        $t = strtotime($fbpost['created_time']);
                        $tm = date('M j,Y' , $t);
                        $postid = explode("_", $fbpost['id']);
                        $fbposts[] = array(
                            'text' => (utf8_strlen(strip_tags(html_entity_decode($fbpost['message'], ENT_QUOTES))) > 60 ? utf8_substr(strip_tags(html_entity_decode($fbpost['message'], ENT_QUOTES)), 0, 60) . '...' : strip_tags(html_entity_decode($fbpost['message'], ENT_QUOTES))),
                            'time' => $tm,
                            'link' => $fbbase . $postid[1]
                        );
                    }
                    $iii++;
                }
            }
        }


        return $fbposts;
    }

    function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}