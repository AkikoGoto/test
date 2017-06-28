<?php /* Smarty version 2.6.18, created on 2015-01-13 14:29:57
         compiled from invoice.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'invoice.html', 34, false),)), $this); ?>
<!-- 
<h1>会社情報検索</h1>
<div>会社IDを入力してください</div>
<form METHOD="POST" action="index.php?action=searchByCompany">
 <input type="text" name="company_id">
 <input type="submit" name="submit" value="<?php echo @COMMON_SUBMIT; ?>
">
</form>
 -->

<h1>新着データ一覧</h1>
<!-- <div><a href="index.php?action=putin">新規にデータを追加する</a></div> -->
<div><span style="font-weight:bold;">請求書払いの契約している会社　台数状況</span></div>

<?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['links']):
?>
<?php echo $this->_tpl_vars['links']; ?>

<?php endforeach; endif; unset($_from); ?>
<table border="1" class="thread">
<tr>
<!-- <td width="25px">ID</td> -->
<td width="30%"><?php echo @COMMON_COMPANY; ?>
</td>
<td>iPhone台数</td>
<td>Android台数</td>
<td>実際のドライバー数</td>
<td>契約開始日</td>
<td>最新ドライバーステータス更新</td>
</tr>
<?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['data']):
?>

<tr>

<!-- <td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->id)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td> -->
<td class="con_even"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td class="con_odd">
	<?php if ($this->_tpl_vars['data']['iphone_device_number']): ?>
		<?php echo ((is_array($_tmp=$this->_tpl_vars['data']['iphone_device_number'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 台
	<?php else: ?>
		0
	<?php endif; ?>
</td>
<td>
	<?php if ($this->_tpl_vars['data']['android_device_number']): ?>
		<?php echo ((is_array($_tmp=$this->_tpl_vars['data']['android_device_number'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 台
	<?php else: ?>
		0
	<?php endif; ?>
</td>
<td class="con_odd">
	<?php if (( $this->_tpl_vars['data']['driver_number'] > $this->_tpl_vars['data']['drivers_number'] ) || ( $this->_tpl_vars['data']['driver_number'] == $this->_tpl_vars['data']['drivers_number'] ) || $this->_tpl_vars['data']['driver_number'] == 0): ?> 
		<?php echo ((is_array($_tmp=$this->_tpl_vars['data']['drivers_number'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

	<?php else: ?>
		<font color="red"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->drivers_number)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</font>
	<?php endif; ?>
</td>
<td ><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['created'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td ><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['last_driver_status_created'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<!-- <td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->updated)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　</td> 
<td>
<?php echo '
<script type="text/javascript">
<!--
function check(){
	return confirm("このデータを削除して、本当によろしいですか？削除すると、元に戻すことはできません。")
}
//-->
<!-- 
</script>	
'; ?>

<?php if ($this->_tpl_vars['from_web'] == NULL): ?>

<FORM METHOD="POST" action="index.php?action=deleteData&id=<?php echo $this->_tpl_vars['data']->id; ?>
&from_web=<?php echo $this->_tpl_vars['from_web']; ?>
" >
<input type="submit" value="削除"
<?php echo 'onclick="return check()"'; ?>

>
</FORM>
<FORM METHOD="POST" action="index.php?action=putin&id=<?php echo $this->_tpl_vars['data']->id; ?>
" >
<input type="submit" value="編集">
</FORM>

<FORM METHOD="POST" action="index.php?action=viewDrivers&id=<?php echo $this->_tpl_vars['data']->id; ?>
" >
<input type="submit" value="ドライバー情報閲覧">
</FORM>

<FORM METHOD="POST" action="index.php?action=viewBranches&id=<?php echo $this->_tpl_vars['data']->id; ?>
" >
<input type="submit" value="営業所情報閲覧">
</FORM>
<FORM METHOD="POST" action="index.php?action=putBranch&company_id=<?php echo $this->_tpl_vars['data']->id; ?>
">
<input type="submit" value="営業所情報追加">
</FORM>
<?php endif; ?>
<?php if ($this->_tpl_vars['from_web'] == 1): ?>
<FORM METHOD="POST" action="index.php?action=putin&id=<?php echo $this->_tpl_vars['data']->id; ?>
&from_web=<?php echo $this->_tpl_vars['from_web']; ?>
" >
<input type="submit" value="仮登録情報承認">
</FORM>
<FORM METHOD="POST" action="index.php?action=deleteData&id=<?php echo $this->_tpl_vars['data']->id; ?>
&from_web=<?php echo $this->_tpl_vars['from_web']; ?>
" >
<input type="submit" value="削除"
<?php echo 'onclick="return check()"'; ?>

>
</FORM>
<?php endif; ?>

</td>
-->
</tr>
<?php endforeach; else: ?>
<tr>
<td colspan="10">
現在記事がありません。
</td>
</tr>
<?php endif; unset($_from); ?>
</table>