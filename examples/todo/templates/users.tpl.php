<form action="" method="POST" id="user">
<input type="hidden" name="user[sid]" value="<?php echo $this->user[sid];?>" />

<label for="user_fname" class="<?php echo $this->invalid_field['first_name'];?>">First Name</label>
<input type="text" name="user[first_name]" value="<?php echo $this->user['first_name'];?>" size="25" id="user_fname" />
<br />
<label for="user_lname" class="<?php echo $this->invalid_field['last_name'];?>">Last Name</label>
<input type="text" name="user[last_name]" value="<?php echo $this->user['last_name'];?>" size="25" id="user_lname" />
<br />
<label for="user_email" class="<?php echo $this->invalid_field['email'];?>">Email</label>
<input type="text" name="user[email]" value="<?php echo $this->user['email'];?>" size="50" id="user_email" />
<br />
<label for="user_password" class="<?php echo $this->invalid_field['password'];?>">Password</label>
<input type="password" name="user[password]" value="" size="25" id="user_password" />
<br />
<label for="user_gender" class="<?php echo $this->invalid_field['gender'];?>">Gender</label>
<select name="user[gender]" id="user_gender">
<option label="Choose:" value="">Choose:</option>
<option label="Male" value="M" <?php echo ($this->user['gender']=='M' ? 'SELECTED' : '');?> >Male</option>
<option label="Female" value="F" <?php echo ($this->user['gender']=='F' ? 'SELECTED' : '');?> >Female</option>
</select>
<br />
<input type="submit" label="Save" value="Save" />
</form>


<table>
<thead>
<th>First</th><th>Last</th><th>Email</th><th>Gender</th>
</thead>
<?php
if ( $this->user_count>0 ) {
	foreach($this->users as $u) {
		echo "<tr>
		<td>{$u['first_name']}</td>
		<td>{$u['last_name']}</td>
		<td>{$u['email']}</td>
		<td>{$u['gender']}</td>
		</tr>
		";
	}
}
echo "<tr><td colspan=4>{$this->user_count} users on file</td></tr>";
?>
</table>