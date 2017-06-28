<?php /* Smarty version 2.6.18, created on 2014-07-09 15:49:56
         compiled from viewDrivers.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'viewDrivers.html', 27, false),)), $this); ?>

<h1><?php echo @COMMON_DRIVERS; ?>
</h1>
<a name="top">&nbsp;</a>

<a href="index.php?action=putDriver&company_id=<?php echo $this->_tpl_vars['company_id']; ?>
" >ドライバーを追加する</a>
<br> 
<?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['links']):
?>
<?php echo $this->_tpl_vars['links']; ?>

<?php endforeach; endif; unset($_from); ?>
<table border="1" class="thread">
<tr>
<td width="25px">ID</td>
<td width="35%"><?php echo @COMMON_NAME; ?>
</td>
<td><?php echo @COMMON_CREATED; ?>
</td>
<td width="60px"><?php echo @BRANCH_NAME; ?>
</td>
<td><?php echo @COMMON_JYUSYO; ?>
</td>
<td><?php echo @COMMON_UPDATE; ?>
</td>
<td></td>
</tr>
<?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['data']):
?>

<tr>

<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['driverId'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td class="con_even"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['last_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['data']['first_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->created)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td class="con_even"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->car_type)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->erea)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
<td class="con_even"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']->updated)) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
　</td>
<td>
<?php echo '
<script type="text/javascript">
<!--
function check(){
	return confirm("このデータを削除して、本当によろしいですか？削除すると、元に戻すことはできません。");
}
//-->
</script>	
'; ?>

<FORM METHOD="POST" action="index.php?action=viewDriver&id=<?php echo $this->_tpl_vars['data']->id; ?>
" 
>
<input type="submit" value="閲覧">
</FORM>

<FORM METHOD="POST" action="index.php?action=deleteDriver&id=<?php echo $this->_tpl_vars['data']->id; ?>
" >
<input type="submit" value="削除"
<?php echo 'onclick="return check()"'; ?>

>
</FORM>

<FORM METHOD="POST" action="index.php?action=putDriver&driver_id=<?php echo $this->_tpl_vars['data']->id; ?>
&company_id=<?php echo $this->_tpl_vars['data']->company_id; ?>
" 
>
<input type="submit" value="編集">
</FORM>

</td>
</tr>
<?php endforeach; else: ?>
<tr>
<td colspan="10">
現在データがありません。
</td>
</tr>
<?php endif; unset($_from); ?>
</table>
