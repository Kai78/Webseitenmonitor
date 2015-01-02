<div class="container" >
	<h2>Monitorbereich</h2>
	<?php $this->load->view('_new_monitor'); $friendly_name_link=""; ?>
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<?php foreach ($monitored_url as $monitored_item):  ?>
		  <div class="panel panel-default">
		  	<div class="panel-heading" role="tab" id="heading<?php echo $monitored_item['id']; ?>">
		  		<h4 class="panel-title">
		  			<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $monitored_item['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $monitored_item['id']; ?>"><?php echo $monitored_item['friendly_name']; echo $monitored_item['is_paused']?"(Überwacht)":"(Pausiert)"; ?></a>
				</h4>
			</div>
			<div id="collapse<?php echo $monitored_item['id']; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $monitored_item['id']; ?>">
      			<div class="panel-body">	
					<a href="<?php echo base_url() . "index.php/monitoring/pause/"; echo $monitored_item['id'] ?>"><?php echo $monitored_item['is_paused']?"Pausieren":"Überwachen"; ?></a><br />
					<!--<a href="<?php echo base_url() . "index.php/monitoring/edit/"; echo $monitored_item['id'] ?>">Bearbeiten</a><br />-->
					<a href="<?php echo base_url() . "index.php/monitoring/delete/"; echo $monitored_item['id'] ?>">Löschen</a><br />
					<a href="#" class="check" id="<?php echo $monitored_item['id'] ?>">Jetzt prüfen</a><br />
					<a href="<?php echo base_url() . "index.php/monitoring/show_details/"; echo $monitored_item['id'] ?>">Zeige Details</a><br />
					<a target="_blank" href="<?php echo $monitored_item['url'] ?>">Gehe zur Website</a><br />
					<!--<?php echo $monitored_item['url']; ?>-->
				</div>
		  	</div>
		</div>
		<?php endforeach ?>
	</div>
</div>

<script type="text/javascript">
    $( document ).ready(function () {
      $(".check").click(function () {
      	var checkId = $(this).attr('id');
        var concatString = "<?php echo base_url() ?>index.php/monitoring/check_now/" + checkId;        
        $.get(concatString, function (httpCode) {
          alert(httpCode);
        });
      });
    });
</script>