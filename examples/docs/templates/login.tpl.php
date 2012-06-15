<div style="margin: 30px auto;width:300px;border:2px solid #909090;text-align:center">

<?php
if ( Core::$auth->is_logged_in ) {
?>
<p>You are currently logged in</p>
<p>
<form method="POST" action="">
<input type="hidden" name="loginout" value="out">
<input type="submit" value="Logout">
</form>
</p>
<?php

} else {

?>
<p>Please login before proceeding</p>
<p>
<form method="POST" action="">
<input type="hidden" name="loginout" value="in">
<input type="submit" value="Login">
</form>
</p>
<?php
}
?>

</div>