<?php header("Content-Type:text/html;charset=Shift_JIS"); ?>
<?php
//==========================================================
//  ���[���t�H�[���V�X�e�� ver.0.96��
//  eWeb http://www.eweb-design.com/
//  Ajax�Ή��J�X�^�}�C�Y
//  AjaxMail http://www.ajaxmail.jp
//==========================================================

// ���̃t�@�C���̖��O
$script ="sendmail.php";

// ���[���𑗐M����A�h���X(�����w�肷��ꍇ�́u,�v�ŋ�؂�)
$to = "kawasaki.koji.mobile@gmail.com";

// ���M����郁�[���̃^�C�g��
$sbj = "�z�[���y�[�W����̖₢���킹";

// ���M�m�F��ʂ̕\��(����=1, ���Ȃ�=0)
$chmail = 1;

// ���M��Ɏ����I�ɃW�����v����(����=1, ���Ȃ�=0)
// 0�ɂ���ƁA���M�I����ʂ��\������܂��B
$jpage = 1;

// ���M��ɃW�����v����y�[�W(���M��ɃW�����v����ꍇ)
$next = "thanks.html";

$from_add = 0;


// �K�{���͍��ڂ�ݒ肷��(����=1, ���Ȃ�=0)
// �����Ƃ��Ă����͂��Ȃ�=0�ɂ��Ă����Ă��������B
$esse = 0;

// �K�{���͍���(���̓t�H�[���Ŏw�肵��name)
$eles = array('�����O','���₢���킹���e');


//--------------------------------------------------------------------
// �ȏ�Ŋ�{�I�Ȑݒ�͏I���ł��B
// �ȉ��̕ύX�͎��ȐӔC�ł��肢���܂��B(�s���̓f�t�H���g��)
// �����͉�ʂ̃��C�A�E�g �� 88�s�ڎ���
// ���M���[���̃��C�A�E�g �� 103�s�ڎ���
// ���o�l�ւ̑��M�m�F���[���̃��C�A�E�g �� 128�s�ڎ���
// ���M�m�F��ʂ̃��C�A�E�g �� 163�s�ڎ���
// ���M�I����ʂ̃��C�A�E�g �� 194�s�ڎ���
// ���M�m�F��ʂ�I����ʂ̃w�b�_�ƃt�b�^ �� 209�s�ڎ���
//--------------------------------------------------------------------

$sendm = 0;
foreach($_POST as $key=>$var) {
  if($var == "eweb_submit") $sendm = 1;
}

// �����̒u������
$string_from = "�_";
$string_to = "�[";

// �����͍��ڂ̃`�F�b�N
if($esse == 1) {
  $flag = 0;
  $length = count($eles) - 1;
  foreach($_POST as $key=>$var) {
    $key = strtr($key, $string_from, $string_to);
    if($var == "eweb_submit") ;
    else {
      for($i=0; $i<=$length; $i++) {
        if($key == $eles[$i] && empty($var)) {
          $errm .= "<FONT color=#ff0000>�u".$key."�v�͕K�{���͍��ڂł��B</FONT><BR>\n";
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
      $errm .= "<FONT color=#ff0000>�u".$eles[$i]."�v�����I���ł��B</FONT><BR>\n";
      $eles[$i] = "eweb_ok";
      $flag = 1;
    }
  }
  if($flag == 1){
    htmlHeader();
?>


<!--- �����͂����������̉�� --- �J�n --------------------->


���̓G���[<BR><BR>
<?php echo $errm; ?>
<BR><BR>
<INPUT type="button" value="�O��ʂɖ߂�" onClick="history.back()">

<!--- �I�� --->


<?php
    htmlFooter();
    exit(0);
  }
}
//--- ���[���̃��C�A�E�g�̕ҏW --- �J�n ------------------->

$body="�u".$sbj."�v����̔��M�ł�\n\n";
$body.="-------------------------------------------------\n\n";
foreach($_POST as $key=>$var) {
  $key = strtr($key, $string_from, $string_to);
  if(get_magic_quotes_gpc()) $var = stripslashes($var);
  if($var == "eweb_submit") ;
  else $body.="[".$key."] ".$var."\n";
}
$body.="\n-------------------------------------------------\n\n";
$body.="���M�����F".date( "Y/m/d (D) H:i:s", time() )."\n";
$body.="�z�X�g���F".getHostByAddr(getenv('REMOTE_ADDR'))."\n\n";

//--- �I�� --->


if($remail == 1) {
//--- ���o�l�ւ̑��M�m�F���[���̃��C�A�E�g�̕ҏW --- �J�n ->

$rebody="���肪�Ƃ��������܂����B\n";
$rebody.="�ȉ��̓��e�����M����܂����B\n\n";
$rebody.="-------------------------------------------------\n\n";
foreach($_POST as $key=>$var) {
  $key = strtr($key, $string_from, $string_to);
  if(get_magic_quotes_gpc()) $var = stripslashes($var);
  if($var == "eweb_submit") ;
  else $rebody.="[".$key."] ".$var."\n";
}
$rebody.="\n-------------------------------------------------\n\n";
$rebody.="���M�����F".date( "Y/m/d (D) H:i:s", time() )."\n";
$reto = $_POST['email'];
$rebody=mb_convert_encoding($rebody,"JIS","Shift_JIS");
$resbj="=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($resbj,"JIS","Shift_JIS"))."?=";
$reheader="From: $to\nReply-To: ".$to."\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();

//--- �I�� --->
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

<!--- ���M�m�F��ʂ̃��C�A�E�g�̕ҏW --- �J�n ------------->


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
        <p class="topic-p-margin">�ȉ��̓��e�ŊԈႢ���Ȃ���΁A�u���M����v�{�^���������Ă��������B</p>
<FORM action="<? echo $script; ?>" method="POST">
<? echo $err_message; ?>
<?php
foreach($_POST as $key=>$var) {
  $key = strtr($key, $string_from, $string_to);
  if (get_magic_quotes_gpc()) $var = stripslashes($var);
  $var = htmlspecialchars($var);
  $key = htmlspecialchars($key);
  print("<p class=cftopic>".$key."�F</p><p class=topic-p-margin>".$var);
?>
<INPUT type="hidden" name="<?= $key ?>" value="<?= $var ?>">
<?php
  print("</p>");
}
?>
<BR>
<INPUT type="hidden" name="eweb_set" value="eweb_submit">
<INPUT type="submit" value="���M����">
<INPUT type="button" value="�O��ʂɖ߂�" onClick="history.back()">
</FORM>
</div>
    </div>
    <div id="content-wrapper"></div>
  </div>
</div>

<!--- �I�� --->

<?php htmlFooter(); } if(($jpage == 0 && $sendm == 1) || ($jpage == 0 && ($chmail == 0 && $sendm == 0))) { htmlHeader(); ?>


<!--- ���M�I����ʂ̃��C�A�E�g�̕ҏW --- �J�n ------------->

���肪�Ƃ��������܂����B<BR>
���M�͖����ɏI�����܂����B<BR><BR>

<!-- ���쌠�\��                                                            -->
<!-- �����Ă��\���܂��񂪁A���̍ۂ͓��T�C�g�Ƀ����N�����\�肭�������B-->
<FONT size="-1"><a href="http://www.ajaxmail.jp" target="_blank">Powered by AjaxMail</a></FONT><BR>


<!--- �I�� --->

<?php htmlFooter(); } else if(($jpage == 1 && $sendm == 1) || $chmail == 0) { header("Location: ".$next); } function htmlHeader() { ?>


<!--- �w�b�_�[�̕ҏW --- �J�n ----------------------------->

<!DOCTYPE html>
<html>
<head>
<meta charset=shift_jis />
<meta name="google-site-verification" content="kxvTcldLoOuDQFs3RrLH8_iR2ovtc38M2MV605E9RUU" />
<title>CONTACT�bKOJI KAWASAKI ���_�i</title>
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

<!--- �I�� --->


<?php } function htmlFooter() { ?>


<!--- �t�b�^�[�̕ҏW --- �J�n ----------------------------->

<footer>
  <p><img src="../common/images/copyright.gif" alt="Copyright&copy;2010 Koji Kawasaki All Rights Reserved. " width="360" height="15"></p>
</footer>
</BODY>
</HTML>

<!--- �I�� --->


<?php } ?>
