<?php /* Smarty version 2.6.18, created on 2014-06-10 16:48:37
         compiled from putin.html */ ?>
<h1><?php echo @EDIT_DATA; ?>
</h1>
<FORM METHOD="POST" action="index.php?action=<?php echo $this->_tpl_vars['action']; ?>
" enctype="multipart/form-data">
<TABLE  class="thread" border="1" id="middle_box">
<TBODY>
	<tr><th width="100px">
	ID
    		</th>
		<td class="con_odd">
		<?php echo $this->_tpl_vars['id']; ?>

		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['id']; ?>
">
 			</td></tr>
			
	<tr><th width="100px">
    		<span class="hissu"><?php echo @COMMON_INDISP; ?>
</span><?php echo @COMMON_IS_COMPANY; ?>
<br>
    		</th>
		<td class="con_odd">
        	<input type="radio" name="is_company" value="0" <?php if ($this->_tpl_vars['data']->is_company == 0): ?>checked<?php endif; ?>><?php echo @COMMON_CORPORATE; ?>
<br>
        	<input type="radio" name="is_company" value="1" <?php if ($this->_tpl_vars['data']->is_company == 1): ?>checked<?php endif; ?>><?php echo @COMMON_INDIVIDUAL; ?>
<br>        	        		
			</td></tr>
			
	<tr><th><span class="hissu"><?php echo @COMMON_INDISP; ?>
</span>    	
    		<?php echo @COMMON_COMPANY; ?>
<br></th>
		<td class="con_odd">
		<input type="text" name="company_name" size="50" maxlength="50" value="<?php echo $this->_tpl_vars['data']->company_name; ?>
<?php echo $this->_tpl_vars['session']['company_name']; ?>
">
       	</td></tr>
        	
<!-- Geographic -->
 <?php if (! $this->_tpl_vars['from_web'] == 1): ?>
	<tr><th>
	<?php echo @COMMON_LAT; ?>
</th>
		<td class="con_odd">
		<?php echo @COMMON_DECIMAL_BASE; ?>
<br>
			<input type="text" size="50" maxlength="100" class="textinput" name="latitude" value="<?php echo $this->_tpl_vars['data']->latitude; ?>
">
		</td></tr>

	<tr><th>
	<?php echo @COMMON_LONG; ?>
</th>
		<td class="con_odd">
		<?php echo @COMMON_DECIMAL_BASE; ?>
<br>
			<input type="text" size="50" maxlength="100" class="textinput" name="longitude" value="<?php echo $this->_tpl_vars['data']->longitude; ?>
">
		</td></tr>
<?php endif; ?>
	<tr><th><span class="hissu"><?php echo @COMMON_INDISP; ?>
</span>
	<?php echo @COMMON_POSTAL; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="postal" value="<?php echo $this->_tpl_vars['data']->postal; ?>
<?php echo $this->_tpl_vars['session']['postal']; ?>
">
		</td></tr>
		
	<tr><th><span class="hissu"><?php echo @COMMON_INDISP; ?>
</span>
	<?php echo @COMMON_PREFECTURE; ?>
</th>
		<td class="con_odd">
		   	<select name="prefecture">
			<?php $_from = $this->_tpl_vars['prefecturesList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['prefecture']):
?>
				<option value="<?php echo $this->_tpl_vars['prefecture']->id; ?>
" 
				<?php if ($this->_tpl_vars['data']->prefecture == $this->_tpl_vars['prefecture']->id): ?> selected<?php endif; ?>
				<?php if ($this->_tpl_vars['session']['prefecture'] == $this->_tpl_vars['prefecture']->id): ?> selected<?php endif; ?>>
                	<?php echo $this->_tpl_vars['prefecture']->prefecture_name; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
			</select>
		</td></tr>

	<tr><th><?php echo @COMMON_CITY; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="city" value="<?php echo $this->_tpl_vars['data']->city; ?>
<?php echo $this->_tpl_vars['session']['city']; ?>
">
		</td></tr>

	<tr><th><?php echo @COMMON_WARD; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="ward" value="<?php echo $this->_tpl_vars['data']->ward; ?>
<?php echo $this->_tpl_vars['session']['ward']; ?>
">
		</td></tr>

	<tr><th><span class="hissu"><?php echo @COMMON_INDISP; ?>
</span>
	<?php echo @COMMON_TOWN; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="town" value="<?php echo $this->_tpl_vars['data']->town; ?>
<?php echo $this->_tpl_vars['session']['town']; ?>
">
		</td></tr>

	<tr><th>
	<?php echo @COMMON_ADDRESS; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="address" value="<?php echo $this->_tpl_vars['data']->address; ?>
<?php echo $this->_tpl_vars['session']['address']; ?>
">
		</td></tr>

	<tr><th><span class="hissu"><?php echo @COMMON_INDISP; ?>
</span>
	<?php echo @COMMON_TEL; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="tel" value="<?php echo $this->_tpl_vars['data']->tel; ?>
<?php echo $this->_tpl_vars['session']['tel']; ?>
">
			<br>
			<?php echo @TEL_NOTICE; ?>

		</td></tr>
<!-- Geographic終わり -->

	<tr><th>
	<?php echo @COMMON_EMAIL; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="email" value="<?php echo $this->_tpl_vars['data']->email; ?>
<?php echo $this->_tpl_vars['session']['email']; ?>
">
		</td></tr>

	<tr><th>
	<?php echo @COMMON_CONTACT_PERSON; ?>
</th>
		<td class="con_odd">
			<input type="text" size="20" maxlength="50" class="textinput" name="contact_person_last_name" 
			value="<?php echo $this->_tpl_vars['data']->contact_person_last_name; ?>
<?php echo $this->_tpl_vars['session']['contact_person_last_name']; ?>
">
			<input type="text" size="20" maxlength="50" class="textinput" name="contact_person_first_name" 
			value="<?php echo $this->_tpl_vars['data']->contact_person_first_name; ?>
<?php echo $this->_tpl_vars['session']['contact_person_first_name']; ?>
">
		</td></tr>

	<tr><th>
	<?php echo @COMMON_CONTACT_TEL; ?>
</th>
		<td class="con_odd">
			<input type="text" size="50" maxlength="100" class="textinput" name="contact_tel" 
			value="<?php echo $this->_tpl_vars['data']->contact_tel; ?>
<?php echo $this->_tpl_vars['session']['contact_tel']; ?>
">
		</td></tr>

	<tr><th>
	<?php echo @COMMON_PWD; ?>
</th>
		<td class="con_odd">
			<input type="password" name="passwd" size="20" class="textinput" value="">
			&nbsp;<?php echo @COMMON_PWD_NOTICE; ?>
<?php echo @COMMON_PWD_EDIT_NOTICE; ?>

		</td></tr>
			
<!-- 送信ボタン -->
	<tr><th colspan="2" align="center">
	<input type=hidden name="activation" value="<?php echo $this->_tpl_vars['activation']; ?>
">
			<input type="submit" name="submit" value="<?php echo @COMMON_SUBMIT; ?>
"></th>
</TBODY>
</TABLE>
</FORM>