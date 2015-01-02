<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#newMonitor">Neuer Monitor</button>
<div class="modal fade" id="newMonitor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Neuer Monitor</h4>
      </div>
      <?php 
      $attributes = array('class' => 'newmonitorFormClass', 'id' => 'newmonitorFormId');
      echo form_open(base_url() . "/index.php/new_monitor", $attributes); 
      ?>
      <div class="modal-body">
      
        <div role="tabpanel">

		  <!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#monitor" aria-controls="monitor" role="tab" data-toggle="tab">&Uuml;berwachung</a></li>
		    <li role="presentation"><a href="#alarm" aria-controls="alarm" role="tab" data-toggle="tab">Alarm</a></li>
		  </ul>

		  <!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="monitor"><!--Monitoreinstellungen-->
			    <div class="form-group">
					<label for="sel1">Monitortyp: </label>
					<select class="form-control" id="sel1">
						<option>HTTP</option>
						<option>Ping</option>
						<option>Port</option>
					</select>
				</div>
				<div class="form-group">
					<label>Anzeigename</label>
					<input type="text" name="friendly_name" value=""  class="form-control" required data-bv-notempty-message="Der Anzeigename muss angegeben werden"/>					
				</div>
				<div class="form-group">
					<label>URL</label>
					<input type="url" name="url_to_monitor" value="" required data-bv-uri-message="Bitte eine korrekte URL eingeben" class="form-control url"/>					
				</div>	
				<div class="form-group">
					<label>Pr&uuml;fungsinterval:</label>
					<input type="number" min="60" max="1200" step="60" name="checkinterval" value="60" />  Sekunden
				</div>	    
		    </div>
		    <div role="tabpanel" class="tab-pane" id="alarm"><!--Alarmeinstellungen-->
		    	<div class="form-group">
					<label>E-Mail:</label>
					<input type="email" name="email_to_alarm" value="" class="form-control required email" required data-bv-emailaddress-message="Bitte ein gültige E-Mail Adresse angeben"/>					
				</div>
		    </div>
		  </div>

		</div>
      </div>
      <div class="modal-footer">
      	<input type="submit" value="Speichern" name="save_new_monitor" class="btn btn-primary"/>
      </div>
      <?php echo form_close()?>
    </div>
  </div>
</div>

