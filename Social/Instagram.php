<?php
namespace App\Social;
class Instagram {

    private $instaaccesstoken;

    function __construct() {
        $this->instaaccesstoken  = config('social.instaaccesstoken');
    }
    
    function RunScript() {
        $json = $this->file_get_contents_curl("https://api.instagram.com/v1/users/self/media/recent/?access_token={$this->instaaccesstoken}");
        return $this->GetData($json);
    }

    function GetData($json) {

        $insta = json_decode($json, true);
        if (isset($insta['data'])) {
            $x = 0;
            foreach($insta['data'] as $item) {
                if ($x < 12) {
                    $instaposts[] = array(
                        'image' => $item['images']['standard_resolution']['url'],
                        'caption' => $item['caption'] ? $item['caption']['text'] : '',
                        'link' => $item['link']
                    );
                }
                $x++;
            }
        } else{
            $instaposts = [];
        }
        return $instaposts;
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