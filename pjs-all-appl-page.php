<div class="wrap">
	<?php

	$url = admin_url('admin.php?page=pjs-plugin/'."pjs-all-appl-page.php");

	add_filter('show_admin_bar', false);
    require_once(plugin_dir_path(__FILE__) . 'pjs-file-info-processing.php');

    function pjs_load_page ($url) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pjs_questions";

		?> <h1>Jautājumi:</h1> <?php
		$user_name = wp_get_current_user()->display_name;
		$table_name = $wpdb->prefix . "pjs_questions";
		$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE pieteicejs = '$user_name' ORDER BY 'datums' ASC");
		?>
		<table> 
			<tr>
				<th>Jautājums</a></th>
				<th>Datums</th>
				<th>Sniegta atbilde</th>
			</tr>
		<?php
		foreach ($db_data as $jaut) { 
			$id = $jaut->id;
			?> 
			<tr>
				<td>
				<?php
				if($jaut->atb != "") {
				?>
					<a id="question-more-click-<?php echo $id; ?>" target="popup">
					<?php echo $jaut->jaut; ?> 
					</a><br>
					<script>
					document.getElementById("question-more-click-<?php echo $id; ?>").addEventListener("click", function(event) {
						var html = "";
						var curHtml = document.getElementById("question-more-info-<?php echo $id; ?>").innerHTML;
						if(curHtml == "") {
							var atb = "<?php echo $jaut->atb; ?>";
							var atbDat = "<?php echo $jaut->atb_datums; ?>";
							html = "<b>Atbilde:</b> " + atb +"<br><b>Atbildes datums:</b> " + atbDat;
						}
						document.getElementById("question-more-info-<?php echo $id; ?>").innerHTML = html;
					});
					</script>
					<div id="question-more-info-<?php echo $id; ?>"></div>
				<?php
				} else {
					?> <p><?php echo $jaut->jaut; ?></p> <?php
				}
				?>
				</td>
				<td> <?php echo $jaut->datums; ?> </td>
				<td> <?php echo ($jaut->sniegta_atb) ? "Jā" : "Nē"; ?> </td>
			</tr>
			<?php
		}
		?> </table>
		<h1>Darbu pieteikumi:</h1> <?php
		$table_name = $wpdb->prefix . "pjs_job_appls";
		$sql = "SELECT * FROM $table_name WHERE pieteicejs = '$user_name'";
		$db_data = $wpdb->get_results($sql);
		?> 	<table> 
			<tr>
				<th>Darba nosaukums</a></th>
				<th>Darba statuss</th>
			</tr>
		<?php
		foreach ($db_data as $appl) { 
			$id = $appl->id;
			?>
			<tr>
				<td><a id="job-appl-more-click-<?php echo $id; ?>" target="popup">
					<?php echo $appl->veic_darbs; ?> 
					</a><br>
					<script>
					document.getElementById("job-appl-more-click-<?php echo $id; ?>").addEventListener("click", function(event) {
						var html = "";
						var curHtml = document.getElementById("job-appl-more-info-<?php echo $id; ?>").innerHTML;
						if(curHtml == "")
							html = "<?php echo pjs_prepare_job_appl_more_info($wpdb, $appl); ?>";
						document.getElementById("job-appl-more-info-<?php echo $id; ?>").innerHTML = html;
					});
					</script>
					<div id="job-appl-more-info-<?php echo $id; ?>"></div>
				</a></td>
				<td>
				<?php
					if (isset($_POST['job-appl-status-ch-'.$id]) && $appl->statuss != "Nepieciešama pārskatīšana") {
						$wpdb->update(
							$table_name,
							array('statuss' => 'Nepieciešama pārskatīšana'), 
							array('id' => $id)
						);
						$appl->statuss = "Nepieciešama pārskatīšana";
					}
					echo $appl->statuss;
					if($appl->statuss == "Pabeigts") {
					?> 
						<form action="<?php echo get_permalink() ?>" method="post" enctype="multipart/form-data">
							<input type="submit" value="Nepieciešama pārskatīšana" name="job-appl-status-ch-<?php echo $id; ?>">
						</form>
					<?php
					}
				?>
				</td>
			</tr>
			<?php
		}
		?> </table> <?php
	}
	function pjs_prepare_job_appl_more_info ($wpdb, $appl) {
		$html = "";
		if($appl->sak_dat != "0000-00-00" && $appl->beigu_dat != "0000-00-00") {
			$html .= "<b>Sākuma datums:</b> $appl->sak_dat;<br>";
			$html .= "<b>Beigu datums:</b> $appl->beigu_dat;<br>";
		}
		if($appl->admin_kom != "") {
			$html .= "<b>Komentārs:</b> $appl->komentars;<br>";
		}
		$file_table_name = $wpdb->prefix . 'pjs_files';
		$file_data = $wpdb->get_results(
			"SELECT * FROM $file_table_name WHERE ent_id = $appl->id AND ent_type = 'pjs_job_appls'"
		);
		if(!empty($file_data)) {
			$html .= "<b>Pielikumi:</b><br>";
			foreach ($file_data as $file) {
				$file_name = basename($file->path);
				$html .= "<a href='$file->path;'>$file_name;</a><br>";
			}
		}
		return $html;
	}
	pjs_load_page($url);
	?>
</div>