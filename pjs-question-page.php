<div class="wrap">
	<?php

	$url = admin_url('admin.php?page=pjs-plugin/'."pjs-question-page.php");

	add_filter('show_admin_bar', false);
	require_once(plugin_dir_path(__FILE__) . 'pjs-question-backend-forms.php');
    require_once(plugin_dir_path(__FILE__) . 'pjs-file-info-processing.php');

    function pjs_load_page ($url) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pjs_questions";

		if (isset($_GET['id'])){
			$page_id = (int) $_GET['id'];
			$url .= "&id=$page_id";
			if($_SERVER["REQUEST_METHOD"] == "POST") {
				if(isset($_POST['pjs_question_submit'])) {
					if(isset($_POST['atbilde'])) {
						$wpdb->update(
							$table_name,
							array(
								'atb' => $_POST["atbilde"],
								'sniegta_atb' => true,
								'atb_datums' => date('Y-m-d H:i:s')
							),
							array('id' => $page_id)
						);
						echo "DATI SAGLABĀTI";
					}
					///Neievieto failus datu bāzē un izmet kļūdu, ja noņem if
					if(isset($_FILE['fails'])) {
						pjs_save_files($wpdb, 'pjs_questions_answer', $page_id);
					}
				}
			}
			$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $page_id");
			pjs_question_form($db_data[0], $url);
		} else {
			?> <h1><?php echo esc_html(get_admin_page_title()); ?></h1> <?php

			$db_data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY 'datums' ASC");
			pjs_question_filters($db_data, $url);
			?> 	
            <table> 
				<tr>
					<th>Jautājums</a></th>
					<th>Datums</th>
					<th>Sniegta atbilde</th>
				</tr>
			<?php
			foreach ($db_data as $jaut) { 
				$page_id = $jaut->id;
				if(pjs_filters($jaut)) {
					?>
					<tr>
						<td><a href="<?php echo $url."&id=$page_id"?>" target="popup">
							<?php echo $jaut->jaut; ?> 
						</a></td>
						<td> <?php echo $jaut->datums; ?> </td>
						<td> <?php echo ($jaut->sniegta_atb) ? "Jā" : "Nē"; ?> </td>
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