<?php header("Content-Type:text/html;charset=Shift_JIS"); ?>
<?php
//==========================================================
//  メールフォームシステム ver.0.96β
//  eWeb http://www.eweb-design.com/
//  Ajax対応カスタマイズ
//  AjaxMail http://www.ajaxmail.jp
//==========================================================

// このファイルの名前
$script ="sendmail.php";

// メールを送信するアドレス(複数指定する場合は「,」で区切る)
$to = "kawasaki.koji.mobile@gmail.com";

// 送信されるメールのタイトル
$sbj = "ホームページからの問い合わせ";

// 送信確認画面の表示(する=1, しない=0)
$chmail = 1;

// 送信後に自動的にジャンプする(する=1, しない=0)
// 0にすると、送信終了画面が表示されます。
$jpage = 1;

// 送信後にジャンプするページ(送信後にジャンプする場合)
$next = "thanks.html";

$from_add = 0;


// 必須入力項目を設定する(する=1, しない=0)
// 原則としてここはしない=0にしておいてください。
$esse = 0;

// 必須入力項目(入力フォームで指定したname)
$eles = array('お名前','お問い合わせ内容');


//--------------------------------------------------------------------
// 以上で基本的な設定は終了です。
// 以下の変更は自己責任でお願いします。(行数はデフォルト時)
// 未入力画面のレイアウト → 88行目周辺
// 送信メールのレイアウト → 103行目周辺
// 差出人への送信確認メールのレイアウト → 128行目周辺
// 送信確認画面のレイアウト → 163行目周辺
// 送信終了画面のレイアウト → 194行目周辺
// 送信確認画面や終了画面のヘッダとフッタ → 209行目周辺
//--------------------------------------------------------------------

$sendm = 0;
foreach($_POST as $key=>$var) {
  if($var == "eweb_submit") $sendm = 1;
}

// 文字の置き換え
$string_from = "＼";
$string_to = "ー";

// 未入力項目のチェック
if($esse == 1) {
  $flag = 0;
  $length = count($eles) - 1;
  foreach($_POST as $key=>$var) {
    $key = strtr($key, $string_from, $string_to);
    if($var == "eweb_submit") ;
    else {
      for($i=0; $i<=$length; $i++) {
        if($key == $eles[$i] && empty($var)) {
          $errm .= "<FONT color=#ff0000>「".$key."」は必須入力項目です。</FONT><BR>\n";
          $flag = 1;
        }
      }
    }
  }
  foreach($_POST as $key=>$var) {
    $key = strtr($key, $string_from, $string_to);
    for($i=0; $i<=$length; $i++) {
      if($key == $eles[$i]) {
        $eles[$i] = "eweb_ok";
      }
    }
  }
  for($i=0; $i<=$length; $i++) {
    if($eles[$i] != "eweb_ok") {
      $errm .= "<FONT color=#ff0000>「".$eles[$i]."」が未選択です。</FONT><BR>\n";
      $eles[$i] = "eweb_ok";
      $flag = 1;
    }
  }
  if($flag == 1){
    htmlHeader();
?>


<!--- 未入力があった時の画面 --- 開始 --------------------->


入力エラー<BR><BR>
<?php echo $errm; ?>
<BR><BR>
<INPUT type="button" value="前画面に戻る" onClick="history.back()">

<!--- 終了 --->


<?php
    htmlFooter();
    exit(0);
  }
}
//--- メールのレイアウトの編集 --- 開始 ------------------->

$body="「".$sbj."」からの発信です\n\n";
$body.="-------------------------------------------------\n\n";
foreach($_POST as $key=>$var) {
  $key = strtr($key, $string_from, $string_to);
  if(get_magic_quotes_gpc()) $var = stripslashes($var);
  if($var == "eweb_submit") ;
  else $body.="[".$key."] ".$var."\n";
}
$body.="\n-------------------------------------------------\n\n";
$body.="送信日時：".date( "Y/m/d (D) H:i:s", time() )."\n";
$body.="ホスト名：".getHostByAddr(getenv('REMOTE_ADDR'))."\n\n";

//--- 終了 --->


if($remail == 1) {
//--- 差出人への送信確認メールのレイアウトの編集 --- 開始 ->

$rebody="ありがとうございました。\n";
$rebody.="以下の内容が送信されました。\n\n";
$rebody.="-------------------------------------------------\n\n";
foreach($_POST as $key=>$var) {
  $key = strtr($key, $string_from, $string_to);
  if(get_magic_quotes_gpc()) $var = stripslashes($var);
  if($var == "eweb_submit") ;
  else $rebody.="[".$key."] ".$var."\n";
}
$rebody.="\n-------------------------------------------------\n\n";
$rebody.="送信日時：".date( "Y/m/d (D) H:i:s", time() )."\n";
$reto = $_POST['email'];
$rebody=mb_convert_encoding($rebody,"JIS","Shift_JIS");
$resbj="=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($resbj,"JIS","Shift_JIS"))."?=";
$reheader="From: $to\nReply-To: ".$to."\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();

//--- 終了 --->
}

$body=mb_convert_encoding($body,"JIS","Shift_JIS");
$sbj="=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($sbj,"JIS","Shift_JIS"))."?=";
if($from_add == 1) {
  $from = $_POST['email'];
  $header="From: ".$to."\nReply-To: ".$to."\nContent-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();
} else {
  $header="Reply-To: ".$to."\nContent-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();
}
if($chmail == 0 || $sendm == 1) {
  mail($to,$sbj,$body,$header);
}
else { htmlHeader();
?>

<!--- 送信確認画面のレイアウトの編集 --- 開始 ------------->


<div id="headbg">
  <div id="wrapper">
    <header>
      <h1><a href="../index.html"><img src="../common/images/logo.gif" width="250" height="97" alt="KOJI KAWASAKI"></a></h1>
      <nav id="ro">
        <ul>
          <li><a href="../index.html" id="navi01">TOP</a></li>
          <li><a href="../profile/index.html" id="navi02">PROFILE</a></li>
          <li><a href="../works/index.html" id="navi03">WORKS</a></li>
          <li><a href="../contact/index.html" id="navi04">CONTACT</a></li>
          <li><a href="http://koji0212.tumblr.com/" target="_blank" id="navi05">BLOG</a></li>
        </ul>
      </nav>
    </header>
    <div id="contact-block">
      <h2><img src="images/contact_title.png" width="140" height="21" alt="CONTACT"></h2>
      <div>
        <p class="topic-p-margin">以下の内容で間違いがなければ、「送信する」ボタンを押してください。</p>
<FORM action="<? echo $script; ?>" method="POST">
<? echo $err_message; ?>
<?php
foreach($_POST as $key=>$var) {
  $key = strtr($key, $string_from, $string_to);
  if (get_magic_quotes_gpc()) $var = stripslashes($var);
  $var = htmlspecialchars($var);
  $key = htmlspecialchars($key);
  print("<p class=cftopic>".$key."：</p><p class=topic-p-margin>".$var);
?>
<INPUT type="hidden" name="<?= $key ?>" value="<?= $var ?>">
<?php
  print("</p>");
}
?>
<BR>
<INPUT type="hidden" name="eweb_set" value="eweb_submit">
<INPUT type="submit" value="送信する">
<INPUT type="button" value="前画面に戻る" onClick="history.back()">
</FORM>
</div>
    </div>
    <div id="content-wrapper"></div>
  </div>
</div>

<!--- 終了 --->

<?php htmlFooter(); } if(($jpage == 0 && $sendm == 1) || ($jpage == 0 && ($chmail == 0 && $sendm == 0))) { htmlHeader(); ?>


<!--- 送信終了画面のレイアウトの編集 --- 開始 ------------->

ありがとうございました。<BR>
送信は無事に終了しました。<BR><BR>

<!-- 著作権表示                                                            -->
<!-- 消しても構いませんが、その際は当サイトにリンクをお貼りください。-->
<FONT size="-1"><a href="http://www.ajaxmail.jp" target="_blank">Powered by AjaxMail</a></FONT><BR>


<!--- 終了 --->

<?php htmlFooter(); } else if(($jpage == 1 && $sendm == 1) || $chmail == 0) { header("Location: ".$next); } function htmlHeader() { ?>


<!--- ヘッダーの編集 --- 開始 ----------------------------->

<!DOCTYPE html>
<html>
<head>
<meta charset=shift_jis />
<meta name="google-site-verification" content="kxvTcldLoOuDQFs3RrLH8_iR2ovtc38M2MV605E9RUU" />
<title>CONTACT｜KOJI KAWASAKI 川崎浩司</title>
<!--[if IE]>
<script src="../common/js/html5.js"></script>

<![endif]-->
<script src="mail/SpryValidationTextField.js" type="text/javascript"></script>
<script src="mail/check.js" type="text/javascript"></script>
<script src="mail/ajaxzip2/prototype.js"></script>
<script src="mail/ajaxzip2/ajaxzip2.js" charset="UTF-8"></script>
<link rel="stylesheet" href="mail/SpryValidationTextField.css" type="text/css" media="all">
<script type="text/javascript" src="../common/js/jquery.js"></script>
<script type="text/javascript" src="../common/js/jquery.cycle.all.js"></script>
<script type="text/javascript" src="../common/js/jquery.blend.js"></script>
<link rel="stylesheet" href="../common/css/html5reset-1.4.1.css" type="text/css" />
<link rel="stylesheet" href="../common/css/base.css" type="text/css" />
<link rel="stylesheet" href="css/contact.css" type="text/css" />
<script type="text/javascript">

jQuery.noConflict();
var j$ = jQuery;

jQuery(document).ready(function(){

	jQuery("#ro a").blend();
	jQuery("#worksro a").blend();

});
</script>
</head>

<body>

<!--- 終了 --->


<?php } function htmlFooter() { ?>


<!--- フッターの編集 --- 開始 ----------------------------->

<footer>
  <p><img src="../common/images/copyright.gif" alt="Copyright&copy;2010 Koji Kawasaki All Rights Reserved. " width="360" height="15"></p>
</footer>
</BODY>
</HTML>

<!--- 終了 --->


<?php } ?>
