<?php
//====================================================================
//Twitter bot script Version 1.3 by @pha
//reply_random.php
//@でreplyをもらったときに、用意した文章からランダムに一行を取り出して返信するスクリプトです。
//これを使ったbotの例：@tentori_（点取り占いbot）

//====================================================================
//設定
//====================================================================

$username = "koji_bot";   //Twitterのユーザー名を書き込んでください
$password = "19830212";  //Twitterのパスワードを書き込んでください   
$file = "re_tw.txt";   //発言を書き込んだファイルの名前（変更可能）
$cron = 3; //cronなどでこのreply.phpをcronなどで実行する間隔を入力してください。単位は分です。

//====================================================================
//高度な設定
//====================================================================

$useReplyPattern = TRUE;   //特定の単語に対して決まった返事をする機能を使うときはTRUE, 使わないときはFALSEにしてください
$replyPatternFile = "reply_pattern.php"; //特定の単語に対して決まった返事をする時に使うファイルの名前（変更可能）
$resOnlyBegginingReply = TRUE; //TRUEだと文頭に自分あての@があったときのみ反応します FALSEだとそうでなくても反応します
$resOnlyNotRT = TRUE;  //TRUEだと文中にRTの文字があると反応しません FALSEだとRTでも反応します

//====================================================================
//設定終わり
//ここから下は編集しないでください
//====================================================================
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="content-language" content="ja" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>reply_random.php</title>
</head>
<body>

<?php
chdir(dirname(__FILE__));
require_once("Services/Twitter.php");
require_once('Services/Twitter/Jsphon/Error.php');
require_once("Services/Twitter/Jsphon/Decoder.php");

$st =& new Services_Twitter($username, $password);
$replydata = $st->getReplies();
$json =& new Jsphon_Decoder();
$replydata = $json->decode($replydata);

//取得する時間の幅
$now = strtotime("now");
$limittime = $now - $cron * 60;

//時間内の返信だけ取り出す
$replies = array();
foreach($replydata as $d){
    $time = strtotime($d["created_at"]);
    if($time === -1){
        $time = strtotime2($d["created_at"]);        
    }
    
    if($limittime <= $time){
        $re["screen_name"] = $d["user"]["screen_name"];
        $re["name"] = $d["user"]["name"];
        $re["text"] = $d["text"];
        $re["id"] = $d["id"];
            
        if($resOnlyBegginingReply){
            if(strpos($re["text"],"@".$username) === 0){ //発言の先頭に@があった場合のみ返答
                $replies[] = $re;
            }                
        }else{
            if($resOnlyNotRT){
                if(strpos($re["text"],"RT") === FALSE){ //RTの文字を含まないときのみ返答
                    $replies[] = $re;
                }                                
            }else{
                $replies[] = $re;                
            }
        }
            
    }
}

//リプライがあった場合のみここからの処理を行う
if(count($replies) != 0){
    
    //リプライの順番を逆にする
    $replies2 = array_reverse($replies);
    
    //発言リストを読み込む
    $tweets = file_get_contents($file);
    $tweets = trim($tweets);
    $tweets = preg_replace("@\n+@","\n",$tweets);
    $tw = explode("\n", $tweets);
       
    //発言をリプライの数だけランダムに選ぶ
    $rand_keys = array();
    for($i=0;$i<count($replies2);$i++){
        $rand_keys[] = array_rand($tw);
    }
    
    //リプライの文章をつくる
    for($i=0;$i < count($replies2);$i++){    
        $text = "";        
        if($useReplyPattern === TRUE){
            require_once($replyPatternFile);
            foreach($reply_pattern as $pattern => $res){
                if(preg_match("@".$pattern."@",$replies2[$i]["text"]) === 1){                                        
                    $text = $res[array_rand($res)];
                    break;
                }
            }            
        }             
        if(empty($text)){
            $text = $tw[$rand_keys[$i]];                            
        }
        
        //時間などを変換する                
        require_once("Services/convert_text.php");
        $text = convert_text($text);

        //idや名前を変換する
        $text = preg_replace("@{name}@u",$replies2[$i]["name"],$text);
        $text = preg_replace("@{id}@u",$replies2[$i]["screen_name"],$text);
        $tweet = preg_replace("@\.?\@[a-zA-Z0-9-_]+\s@u","",$replies2[$i]["text"]);
        $text = preg_replace("@{tweet}@u",$tweet,$text);
        
        $message = "@".$replies2[$i]["screen_name"]." ".$text;
        $in_reply_to_status_id = $replies2[$i]["id"];        
        
        //投稿する
        $result = $st->setUpdate(array('status'=>$message,'in_reply_to_status_id'=>$in_reply_to_status_id));        
        
        if($result){
            echo "Twitterへの投稿に成功しました。<br />";
            echo "@<a href='http://twitter.com/{$username}' target='_blank'>{$username}</a>に投稿したメッセージ：{$message}<br />";
        }else{
            echo "Twitterへの投稿に失敗しました。パスワードやユーザー名をもう一度チェックしてみてください。<br />";        
            echo "ユーザー名：@<a href='http://twitter.com/{$username}' target='_blank'>{$username}</a><br />";
            echo "投稿しようとしたメッセージ：{$message}<br />";
        }
    }

}else{
    echo $cron."分以内に受け取った@はないようです。<br />";    
}

function strtotime2($time){
    $time2 = preg_replace("@\+([0-9]{4})\s@","",$time);
    $time2 = strtotime($time2) + 32400;
    return($time2);    
}
?>

</body>
</html>

<?php
/*
Twitter bot script Version 1.1
reply_random.php

配布URL：http://pha22.net/text/twitterbot.html
作者：pha (pha.japan@gmail.com)
感想、要望などは
http://d.hatena.ne.jp/pha/20090916/twitterbot
のコメント欄にくださいー

【利用条件について】
・このスクリプトはPHPライセンス3.01に基づいて公開されています。
・このスクリプトの使用は商用、非商用に関わらず一切自由です。著作権表示を消さない限り、スクリプトの改造・再配布も自由にしていただいて構いません。
*/

/**
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @author    pha <pha.japan@gmail.com>
 * @copyright 2009 pha <pha.japan@gmail.com>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.transrain.net/product/services_twitter/
 * This product includes PHP, freely available from http://www.php.net/
 */
?>