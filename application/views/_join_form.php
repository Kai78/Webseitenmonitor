<div class="container">

	<div class="row">	

	<h2>Hier können Sie sich registrieren</h2>
		<div class="col-md-4">
			<!--<?php echo form_open(base_url()."index.php/register/")?>--> 
			<?php echo  form_open(base_url() . "index.php/register")?>
				<span><?php echo $captcha_return?><?php echo validation_errors() ?></span>
				<div class="form-group">
					<label>Ihr Name:</label>
					<input type="text" name="name" value="<?php echo set_value('name') ?>" class="form-control"/>					
				</div>
				
				<div class="form-group">
					<label>Ihr gewünschter Benutzername:</label>
					<input type="text" name="username" value="<?php echo set_value('username') ?>" class="form-control"/>					
				</div>

				<div class="form-group">
					<label>Ihr gewünschtes Passwort:</label>
					<input type="password" name="password" value="<?php echo set_value('password') ?>" class="form-control"/>					
				</div>

				<div class="form-group">
					<label>Bestätigen Sie Ihr Passwort hier:</label>
					<input type="password" name="passconf" value="<?php echo set_value('passconf') ?>" class="form-control"/>					
				</div>
				
				<div class="form-group">
					<label>Ihre E-Mail:</label>
					<input type="text" name="email" value="<?php echo set_value('email') ?>" class="form-control"/>					
				</div>
				
				<div class="form-group">
					<label>Bitte geben Sie den Sicherheitscode ein:</label>
					<?php echo $cap_img; ?><br /><br />
					<input type="text" name="captcha" value="" class="form-control"/>					
				</div>
				<!--
				 <div class="checkbox">
				 	<label>
						<input type="checkbox" name="terms" value="1" <?php echo set_checkbox('terms', '1'); ?> class="form-control" />Ich stimme den Übereinstimmungen zu
					</label>
				</div>
				-->
				<div class="text-center">	
					<input type="submit" value="Submit" name="submit" class="btn btn-primary btn-lg"/>
				</div>
			<?php echo form_close()?>
		</div>
	</div>
</div>

