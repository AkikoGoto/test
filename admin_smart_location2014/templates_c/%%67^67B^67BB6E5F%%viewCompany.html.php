<?php /* Smarty version 2.6.18, created on 2015-01-13 14:00:56
         compiled from viewCompany.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'viewCompany.html', 9, false),)), $this); ?>

<h1>会社情報</h1>

	<TABLE class="thread" border="1" id="middle_box">
		<TBODY>
			<tr>
				<th width="120px">ID</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type=hidden name="id"
					value="<?php echo ((is_array($_tmp=$this->_tpl_vars['id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th width="100px"><?php echo @COMMON_IS_COMPANY; ?>
</th>
				<td class="con_odd"><?php if ($this->_tpl_vars['is_company'] == 0): ?>
				<?php echo @COMMON_INDIVIDUAL; ?>
 
					<?php elseif ($this->_tpl_vars['is_company'] == 1): ?>
					<?php echo @COMMON_CORPORATE; ?>
 
					<?php else: ?>
					<?php echo @ERROR_TITLE; ?>
 <?php endif; ?> <input type=hidden
					name="is_company" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['is_company'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>
			
			<tr>
				<th><?php echo @COMMON_GROUP_ID; ?>
<br></th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['company_group_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type=hidden
					name="company_group_id" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['company_group_id'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>
			
			<tr>
				<th><?php echo @COMMON_COMPANY; ?>
<br></th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type=hidden
					name="company_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<!-- Geographic -->
			<tr>
				<th><?php echo @COMMON_PREFECTURE; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['prefecture_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
			</tr>
			<tr>
				<th><?php echo @COMMON_CITY; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['city'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden" name="city"
					value="<?php echo ((is_array($_tmp=$this->_tpl_vars['city'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th><?php echo @COMMON_WARD; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['ward'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden" name="ward"
					value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ward'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th><?php echo @COMMON_TOWN; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['town'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden" name="town"
					value="<?php echo ((is_array($_tmp=$this->_tpl_vars['town'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th><?php echo @COMMON_ADDRESS; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['address'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden"
					name="address" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['address'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th><?php echo @COMMON_TEL; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['contact_tel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden" name="tel"
					value="<?php echo ((is_array($_tmp=$this->_tpl_vars['tel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>


			<tr>
				<th><?php echo @COMMON_EMAIL; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden"
					name="email" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>
			
				
			<tr>
				<th><?php echo @WHAT_BILL; ?>
</th>
				<td class="con_odd">
					<?php if ($this->_tpl_vars['invoice'] == 0): ?>
						<?php echo @NO; ?>

					<?php elseif ($this->_tpl_vars['invoice'] == 1): ?>
						<?php echo @YES; ?>

					<?php endif; ?>
					<input type="hidden" name="invoice" value="<?php echo $this->_tpl_vars['invoice']; ?>
">
				</td>
			</tr>		
			
			<tr><th>
				iPhone台数</th>
				<td class="con_odd">
					<?php echo $this->_tpl_vars['iphone_device_number']; ?>
 台
					<input type="hidden" class="textinput" name="iphone_device_number" value="<?php echo $this->_tpl_vars['iphone_device_number']; ?>
">
		        	<br>  
			</td></tr>
			
			<tr><th>
			Android台数</th>
				<td class="con_odd">
					<?php echo $this->_tpl_vars['android_device_number']; ?>
 台
					<input type="hidden" class="textinput" name="android_device_number" value="<?php echo $this->_tpl_vars['android_device_number']; ?>
">
		        	<br>  
				</td></tr>
						
			<tr>
				<th><?php echo @COMMON_CONTACT_PERSON; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['contact_person_last_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['contact_person_first_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

					<input type="hidden" name="contact_person_last_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['contact_person_last_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
					<input type="hidden" name="contact_person_first_name" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['contact_person_first_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th><?php echo @COMMON_CONTACT_TEL; ?>
</th>
				<td class="con_odd"><?php echo ((is_array($_tmp=$this->_tpl_vars['mobile_tel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 <input type="hidden"
					name="contact_tel" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['contact_tel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				</td>
			</tr>

			<tr>
				<th><?php echo @COMMON_PWD; ?>
</th>
				<td class="con_odd"><?php echo @PWD_NO_DISPLAY; ?>
</td>
			</tr>
			<!-- Geographic終わり -->
			
				<tr><th>
				<?php echo @COMMON_MESSAGE; ?>
欄</th>
					<td class="con_odd">
						<?php echo ((is_array($_tmp=$this->_tpl_vars['remarks'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

						<input type="hidden" name="remarks" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['remarks'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"> 			
					</td></tr>
		</TBODY>
	</TABLE>

<!-- <div id="map_canvas" style="width: 500px; height: 300px"></div>
<?php echo $this->_tpl_vars['geocode_address_div']; ?>
 -->