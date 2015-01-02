<div class="container">
	<h1>Statistik der Website <?php echo $url; ?></h1>
	<br/>
	<div class="row">
		<div class="col-md-4">
			<h2>Allgemein</h2>
			<?php foreach($statistic as $measureItem => $measureValue):?>
				<div >
					<label><?php echo $measureItem; ?></label>
					<p><?php echo $measureValue; ?></p>
				</div>
			<?php endforeach; ?>
			</div>

		<div class="col-md-8">
			<h2>Antwortzeiten der letzten drei Monate</h2>
			<!--<?php print_r($response_times)?>-->
			<table class="table">
				<thead>
					<tr>
						<th>Datum</th>
						<th>Zeit (Sekunden)</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($response_times as $measureItem):?>
						<tr>
							<td><?php echo $measureItem['data_of_response']; ?></td>
							<td><?php echo $measureItem['time_of_response']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>