<div class="wrap">
	<?php
	?> <h1><?php echo esc_html(get_admin_page_title()); ?></h1> <?php

	$url = admin_url('admin.php?page=pjs-plugin/'."pjs-job-perf-page.php");

	add_filter('show_admin_bar', false);
	require_once(plugin_dir_path(__FILE__) . 'pjs-job-appl-backend-forms.php');

    function pjs_load_job_appl ($url) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pjs_job_appls";

		if (isset($_GET['id'])){
			$page_id = (int) $_GET['id'];
			$url .= "&id=$page_id";
			if($_SERVER["REQUEST_METHOD"] == "POST") {
				$date1 = date_create($_POST['sakuma-datums']);
				$date2 = date_create($_POST['beigu-datums']);
				$todays_date = date_create(date("m/d/Y"));
				if (isset($_POST['sakuma-datums']) && $_POST['statuss'] == "NULL")
					$_POST["statuss"] = "Ieplānots";
				if ($date1 < $date2 && $date1 >= $todays_date) {
					$wpdb->update(
						$table_name,
						array(
							'veic_darbs' => $_POST["veic_darbs"],
							'statuss' => $_POST["statuss"],
							'sak_dat' => $_POST["sakuma-datums"],
							'beigu_dat' => $_POST["beigu-datums"]
						), 
						array('id' => $page_id)
					);
					echo "DATI SAGLABĀTI!!!!";
				}
			}
			$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $page_id");
			pjs_job_appl_form($db_data[0], $url, $wpdb);
		} else {
			$db_data = $wpdb->get_results("SELECT * FROM $table_name");
			pjs_job_appl_filters($db_data, $url);
			?> 	<table> 
				<tr>
					<th>Darba nosaukums</a></th>
					<th>Darba statuss</th>
					<th>Ieplānotais sākuma datums</th>
					<th>Ieplānotais beigus datums</th>
				</tr>
			<?php
			foreach ($db_data as $appl) { 
				$page_id = $appl->id;
				if(pjs_filters($appl)) {
					if(isset($_POST["statuss"])) {
						if ($_POST["statuss"] == "Ieplānots" || $_POST["statuss"] == "Izpildē" ) {
						?>
						<tr>
							<td><a href="<?php echo $url."&id=$page_id"?>" target="popup">
								<?php echo $appl->veic_darbs; ?> 
							</a></td>
							<td> <?php echo $appl->statuss; ?> </td>
							<td> <?php echo $appl->sak_dat; ?> </td>
							<td> <?php echo $appl->beigu_dat; ?> </td>
						</tr>
						<?php
						}
					}
				}
			}
			?> </table> <?php
		}
	}
	pjs_load_job_appl($url);
	?>
</div>