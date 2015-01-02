<div class="container">
	<div class="row">
			<h2>Hier können Sie sich einloggen</h2>	
	    <div class="col-md-4">
			<?php echo form_open(base_url() . 'index.php/login/')?>

			<?php echo validation_errors(); ?>
			<span><b><?php echo $login_failed; ?></b></span>

			<div class="form-group">
				<label>Benutzername oder E-mail:</label>
				<input type="text" name="username" value="<?php echo set_value('username'); ?>" class="form-control"/>
			</div>

			<div class="form-group">
				<label>Passwort:</label>
				<input type="password" name="password" value="<?php echo set_value('password'); ?>" class="form-control"/>	
			</div>

			<div class="text-center">		
				<input type="submit" value="Login" name="submit_login" class="btn btn-primary btn-lg"/>			
			</div>
			<?php echo form_close()?>
		</div>
	</div>
</div>