<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1> <?php

	$url = admin_url('admin.php?page=pjs-plugin/'."pjs-job-appl-page.php");

	add_filter('show_admin_bar', false);
	require_once(plugin_dir_path(__FILE__) . 'pjs-job-appl-backend-forms.php');
    function pjs_load_page ($url) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pjs_job_appls";

		if (isset($_GET['id'])){
			$page_id = (int) $_GET['id'];
			$url .= "&id=$page_id";
			if($_SERVER["REQUEST_METHOD"] == "POST") {
				if(isset($_POST["saglabat"])) {
					$date1 = date_create($_POST['sakuma-datums']);
					$date2 = date_create($_POST['beigu-datums']);
					$todays_date = date_create(date("m/d/Y"));
					if (isset($_POST['sakuma-datums']) && $_POST['statuss'] == "NULL")
						$_POST["statuss"] = "Ieplānots";
					if ($date1 < $date2 && $date1 >= $todays_date) {
						$wpdb->update(
							$table_name,
							array(
								'statuss' => $_POST["statuss"],
								'sak_dat' => $_POST["sakuma-datums"],
								'beigu_dat' => $_POST["beigu-datums"],
								'prio' => $_POST["prioritate"],
								'komentars' => $_POST["admin_kom"]
							), 
							array('id' => $page_id)
						);
						echo "DATI SAGLABĀTI";
					}
				} else if(isset($_POST["pievienot"])) {
					$worker_t_name = $wpdb->prefix . "pjs_workers_empl";
					$worker_name = $_POST['darba-veicejs'];
					echo $worker_name;
					$result = $wpdb->get_row("SELECT * FROM $worker_t_name WHERE worker = '$worker_name'");
					if (empty($result) && $_POST['darba-veicejs'] != "NULL") {
						$wpdb->insert(
							$worker_t_name,
							array(
								'job_appl_id' => $page_id,
								'worker' => $worker_name
							)
						);
					}
				}
			}
			$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $page_id");
			pjs_job_appl_form($db_data[0], $url, $wpdb);
		} else {
			$sql = "SELECT * FROM $table_name";
			$db_data = $wpdb->get_results($sql);
			pjs_job_appl_filters($db_data, $url);
			?> <table> 
				<tr>
					<th>Darba numurs</th>
					<th>Darba nosaukums</a></th>
					<th>Pieteicējs</th>
					<th>Darba statuss</th>
				</tr>
			<?php
			foreach ($db_data as $appl) { 
				$page_id = $appl->id;
				if(pjs_filters($appl)) {
					?>
					<tr>
						<td> <?php echo $page_id; ?> </td>
						<td><a href="<?php echo $url."&id=$page_id"?>" target="popup">
							<?php echo $appl->veic_darbs; ?> 
						</a></td>
						<td> <?php echo $appl->pieteicejs; ?> </td>
						<td> <?php echo $appl->statuss; ?> </td>
					</tr>
					<?php
				}
			}
			?> </table> <?php
		}
	}
	pjs_load_page($url);
	?>
</div>