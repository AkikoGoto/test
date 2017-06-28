<?php /* Smarty version 2.6.18, created on 2014-08-07 15:05:21
         compiled from template.html */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta http-equiv="content-style-type" content="text/css" />
<meta name="keyword" contents=""  />
<meta name="robots" contents="none,noindex"  />
<script type="text/javascript" src="../templates/js/jquery-1.3.2.min.js"></script> 
<?php echo $this->_tpl_vars['js']; ?>

<link rel="shortcut icon" href="templates/image/favicon.ico">
<TITLE>Smart動態管理　管理画面 <?php echo $this->_tpl_vars['page_title']; ?>
</TITLE>
 <LINK rel="stylesheet" href="templates/default.css" type="text/css">
</head>
<body <?php echo $this->_tpl_vars['onload_js']; ?>
>
<div align="center">
<table class="main" cellpadding="0px" cellspacing="0px" border="0">
<tr>
<td ><table class="logo"><tr><td >
<a href="index.php">
<h1>Smart動態管理　管理画面</h1>
</a>
</td>

</table>
</td>
</tr>
<tr>
<td class="link_side">  <div class="link">
<a href="index.php">トップ</a>&nbsp;|

<a href="index.php?action=invoice">請求書</a>&nbsp;|

<?php if ($this->_tpl_vars['u_id'] != NULL): ?>
<a href="index.php?action=logout">
ログアウト</a>
<?php else: ?>
<a href="index.php?action=login">
ログイン</a>
<?php endif; ?>
</div>
</td>
</tr>

<tr><td>
<table class="pink" cellpadding="2" cellspacing="3">
<tr>
<td class="contents">

<?php if ($this->_tpl_vars['message']): ?><div class="message"><?php echo $this->_tpl_vars['message']; ?>
</div><?php endif; ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['filename'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</td>
</tr>
<tr><td class="footer" colspan="2">
<div class="footer"><a href="index.php"> 管理画面TOPへ</a>|<a href="../index.php"> Smart動態管理 管理画面へ</a>
</div>
</td></tr>
</table>

</td></tr>
</table>
</div>
</body>

</html>