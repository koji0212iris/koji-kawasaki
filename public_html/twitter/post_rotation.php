<?php
//====================================================================
//Twitter bot script Version 1.3 by @pha
//post_rotation.php
//用意した文章を上から一行ずつ順番にtwitterに投稿するスクリプトです。
//これを使ったbotの例：@hrdaya（般若心経をエンドレスに唱え続けるbot）
//小説や詩などの文章を順番に配信するのに向いています

//====================================================================
//設定
//====================================================================

$username = "Twitterのユーザー名";   //Twitterのユーザー名を書き込んでください
$password = "Twitterのパスワード";    //Twitterのパスワードを書き込んでください   
$file = "tw.txt";       //発言を書き込んだファイルの名前（変更可能）。サーバーにアップロードしたらパーミッションを666に設定してください。  
$end = "一番最後に発言させたい文章をここに書く"; //投稿をエンドレスにループさせたくない場合、一番最後の発言を書き込んでください。設定しないと（「$end ="";」にすると）エンドレスにループします。
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
<title>post_rotation.php</title>
</head>
<body>

<?php
chdir(dirname(__FILE__));
require_once("Services/Twitter.php");

//発言リストを読み込む
$tweets = file_get_contents($file);
$tweets = trim($tweets);
$tweets = preg_replace("@\n+@","\n",$tweets);
$tw = explode("\n", $tweets);

//発言を一つ選ぶ
$message = $tw[0];

//時間などを変換する
require_once("Services/convert_text.php");
$message = convert_text($message);

//twitterに投稿する
$st =& new Services_Twitter($username, $password);
$result = $st->setUpdate($message);

//結果の表示
if($result){
    echo "Twitterへの投稿に成功しました。<br />";
    echo "@<a href='http://twitter.com/{$username}' target='_blank'>{$username}</a>に投稿したメッセージ：{$message}";
}else{
    echo "Twitterへの投稿に失敗しました。パスワードやユーザー名をもう一度チェックしてみてください。<br />";        
    echo "ユーザー名：@<a href='http://twitter.com/{$username}' target='_blank'>{$username}</a><br />";
    echo "投稿しようとしたメッセージ：{$message}";
}

//ログの順番を並び替える（postしたものを一番最後に持って来る）
if($message !== $end){
    $tw_ = explode("\n", $tweets);
    $tw2 = array();
    for($i=0;$i<count($tw_) - 1;$i++){
        $tw2[$i] = $tw_[$i+1];
    }
    $tw2[] = $tw_[0];
    $tweets2 = "";
    foreach($tw2 as $t){
        $tweets2 .= $t."\n";
    }
    $tweets2 = trim($tweets2);
    
    //順番を並び替えたログを書き込む
    $fp = fopen($file, 'w');
    fputs($fp, $tweets2);
    fclose($fp);    
}
?>

</body>
</html>

<?php
/*
Twitter bot script Version 1.1
post_rotation.php

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