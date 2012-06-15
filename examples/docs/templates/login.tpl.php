<div style="margin: 30px auto;width:300px;border:2px solid #909090;text-align:center">

<?php
if ( Core::$auth->is_logged_in ) {
?>
<p>You are currently logged in</p>
<p>
<form method="POST" action="">
<input type="hidden" name="loginout" value="out">
<button type="submit" class="btn" value="Logout" >Logout</button>
</form>
</p>
<?php

} else {

?>
<p>Please login before proceeding</p>
<p>
<form method="POST" action="">
<input type="hidden" name="loginout" value="in">
<button value="Login" type="submit" class="btn" >Login</button>
</form>
</p>
<?php
}
?>

</div>
