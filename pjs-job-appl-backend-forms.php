<?php
    function pjs_job_appl_form ($db_job_appl, $page_link, $wpdb) {
        require_once(plugin_dir_path(__FILE__) . 'pjs-file-info-processing.php');
		?>
            <form method="POST" action="<?php echo $page_link; ?>">
                <!-- Pieteicējs -->
                <b>Pieteicējs:</b>
                <p style="display:inline"> <?php echo $db_job_appl->pieteicejs; ?> </p>
                <br>

                <!-- Veicamais darbs -->
                <b>Nosaukums:</b>
                <p style="display:inline"> <?php echo $db_job_appl->veic_darbs; ?> </p>
                <br>
                
                <!-- E-pasts -->
                <b>Pieteicēja e-pasts:</b>
                <p style="display:inline"> <?php echo $db_job_appl->epasts; ?> </p>
                <br>

                <!-- Darba apraksts -->
                <b>Apraksts:</b>
                <p style="display:inline"> <?php echo $db_job_appl->apraksts; ?> </p>
                <br>

                <!-- Darba statuss -->
                <b>Statuss:</b><br>
                <select name="statuss" id="statuss">
                    <option value="NULL"></option>
                    <option value="Pieteikts" <?php echo ($db_job_appl->statuss == "Pieteikts") ? "selected" : ""; ?>>
                        Pieteikts
                    </option>
                    <option value="Ieplānots" <?php echo ($db_job_appl->statuss == "Ieplānots") ? "selected" : ""; ?>>
                        Ieplānots
                    </option>
                    <option value="Izpildē" <?php echo ($db_job_appl->statuss == "Izpildē") ? "selected" : ""; ?>>
                        Izpildē
                    </option>
                    <option value="Pabeigts" <?php echo ($db_job_appl->statuss == "Pabeigts") ? "selected" : ""; ?>>
                        Pabeigts
                    </option>
                    <option value="Nepieciešama pārskatīšana" 
                    <?php echo ($db_job_appl->statuss == "Nepieciešama pārskatīšana") ? "selected" : ""; ?>>
                        Nepieciešama pārskatīšana
                    </option>
                </select> 
                <br>

                <!-- Datumi -->
                <b>Sākuma datums:</b><br>
                <input type="date" id="sakuma-datums" name="sakuma-datums" value="<?php echo $db_job_appl->sak_dat; ?>"> 
                <br>
                
                <b>Beigu Datums:</b><br>
                <input type="date" id="beigu-datums" name="beigu-datums" value="<?php echo $db_job_appl->beigu_dat; ?>"> 
                <br>

                <!-- Prioritāte -->
                <b>Prioritāte:</b><br>
                <select name="prioritate" id="prioritate">
                    <option value="NULL">...</option>
                    <option value="Augsta" <?php echo ($db_job_appl->prio == "Augsta") ? "selected" : ""; ?>>Augsta</option>
                    <option value="Normāla" <?php echo ($db_job_appl->prio == "Normāla") ? "selected" : ""; ?>>Normāla</option>
                    <option value="Zema" <?php echo ($db_job_appl->prio == "Zema") ? "selected" : ""; ?>>Zema</option>
                </select> 
                <br><br>

                <!-- Faili -->
                <b>Pievienotie faili:</b><br>
                <?php
                    pjs_load_file_links($db_job_appl->id, 'pjs_job_appls');
                ?>
                <br>

                <!-- Komentārs -->
                <b>Komentārs:</b><br>
                <textarea class="textarea" name="komentars" id="komentars" rows="10" cols="30" minlength="10"
                placeholder="Brīvā formā uzrakstiet savu jautājumu!" required>
                <?php echo $db_job_appl->admin_kom; ?>
                </textarea>
                <br><br>

                <!-- Darba veicēji -->
                <b>Darba veicēji:</b><br>
                <?php
                    $table_name = $wpdb->prefix . "pjs_workers_empl";
                    $db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE job_appl_id = $db_job_appl->id");
                    foreach ($db_data as $empl_worker) {
                        echo "<p>$empl_worker->worker</p>";
                    }
                    $args = array('role' => 'worker');
                    $users = get_users($args);
                ?>
                <select name="darba-veicejs" id="darba-veicejs">
                    <option value="NULL">...</option>
                    <?php
                    foreach ($users as $user) {
                        ?>
                        <option value="<?php echo $user->display_name; ?>" 
                        <?php echo (pjs_get_on_request("darba-veicejs") == $user->display_name) ? "selected" : ""; ?>>
                            <?php echo $user->display_name; ?>
                        </option>
                        <?php
                    }
                ?>
                </select> 
                <!-- Pievienot -->
                <input type="submit" value="Pievienot" name="pievienot">
                <br><br>

                <!-- Saglabāt -->
                <input type="submit" value="Saglabāt" name="saglabat">
                <br><br>
            </form>
		<?php
	}
    function pjs_get_on_request ($id) {
        return ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST[$id])) ? $_POST[$id] : "";
    }
    function pjs_filters ($appl) {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $piet = true;
            $st = true;
            $search = false;
            if(!isset($_POST["darb-mekl"]) || $_POST["darb-mekl"] == "" ||
                stripos($appl->veic_darbs, $_POST["darb-mekl"]) !== false)
                $search = true;
            if(isset($_POST["pieteicejs"]))
                if($_POST["pieteicejs"] != "NULL")
                    if($_POST["pieteicejs"] != $appl->pieteicejs)
                        $piet = false;
            if(isset($_POST["statuss"]))
                if($_POST["statuss"] != "NULL")
                    if($_POST["statuss"] != $appl->statuss)
                        $st = false;
            return ($piet && $st && $search);
        }
        return true;
    }
    function pjs_job_appl_filters ($db_data, $url) {
        ?>
			<form method="POST" action="<?php echo $url; ?>">
				<button type="submit">Meklēt</button>
				<input type="text" placeholder="Meklēt pēc darba nosaukuma..." name="darb-mekl" 
				style="width: 300px;">

                <select name="pieteicejs" id="pieteicejs">
                    <option value="NULL">...</option>
                    <?php
                    $piet = array();
                    foreach ($db_data as $appl) {
                        if(!in_array($appl->pieteicejs, $piet)) {
                            array_push($piet, $appl->pieteicejs);
                            ?>
                            <option value="<?php echo $appl->pieteicejs; ?>" 
                            <?php echo (pjs_get_on_request("pieteicejs") == $appl->pieteicejs) ? "selected" : ""; ?>>
                                <?php echo $appl->pieteicejs; ?>
                            </option>
                            <?php
                        }
                    }
                ?>
                </select>
                <select name="statuss" id="statuss">
                    <option value="NULL">...</option>
                    <option value="Pieteikts" <?php echo (pjs_get_on_request("statuss") == "Pieteikts") ? "selected" : ""; ?>>
                        Pieteikts
                    </option>
                    <option value="Ieplānots" <?php echo (pjs_get_on_request("statuss") == "Ieplānots") ? "selected" : ""; ?>>
                        Ieplānots
                    </option>
                    <option value="Izpildē" <?php echo (pjs_get_on_request("statuss") == "Izpildē") ? "selected" : ""; ?>>
                        Izpildē
                    </option>
                    <option value="Pabeigts" <?php echo (pjs_get_on_request("statuss") == "Pabeigts") ? "selected" : ""; ?>>
                        Pabeigts
                    </option>
                    <option value="Nepieciešama pārskatīšana" 
                    <?php echo (pjs_get_on_request("statuss") == "Nepieciešama pārskatīšana") ? "selected" : ""; ?>>
                        Nepieciešama pārskatīšana
                    </option>
                </select> 
			</form> <br>
		<?php
    }
?>