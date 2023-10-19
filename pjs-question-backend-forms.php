<?php
    function pjs_question_form ($db_question, $page_link) {
        require_once(plugin_dir_path(__FILE__) . 'pjs-file-info-processing.php');
		?>
            <form method="POST" action="<?php echo $page_link; ?>">
                <!-- Jautājums -->
                <b>Jautājums:</b>
                <p style="display:inline"> <?php echo $db_question->jaut; ?> </p>
                <br>
                
                <!-- Pieteicējs -->
                <b>Pieteicējs:</b>
                <p style="display:inline"> <?php echo $db_question->pieteicejs; ?> </p>
                <br>
                
                <!-- E-pasts -->
                <b>Pieteicēja e-pasts:</b>
                <p style="display:inline"> <?php echo $db_question->epasts; ?> </p>
                <br>

                <!-- Datumi -->
                <b>Datums:</b>
                <p style="display:inline"> <?php echo $db_question->datums; ?> </p> 
                <br>

                <!-- Jautājuma pielikumi -->
                <b>Jautājuma pielikumi:</b><br>
                <?php
                    pjs_load_file_links($db_question->id, "pjs_questions");
                ?>
                <!-- Atbilde -->
                <b>Atbilde:</b><br>
                <textarea class="textarea" name="atbilde" id="atbilde" rows="10" cols="30" 
                placeholder="Atbilde uz jautājumu..." required>
                <?php echo $db_question->atb; ?>
                </textarea>
                <br><br>

                <!-- Atbildes pielikumi -->
                <b>Atbildes pielikumi:</b><br>
                <?php
                    pjs_load_file_links($db_question->id, "pjs_question_answer", true);
                ?>
                <label for="fails"> Atbildes pielikuma faili (Max. 5 MB):</label><br>
                <label for="fails">Atļautie failu tipi: .jpg; .png; .doc; .pdf</label><br>
                <input type="file" id="fails" name="fails[]" multiple>
                <br><br>

                <br>
                <!-- Poga -->
                <input type="submit" value="Saglabāt" name="pjs_question_submit">
                <br><br>
            </form>
		<?php
	}
    function pjs_get_on_request ($id) {
        return ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST[$id])) ? $_POST[$id] : "";
    }
    function pjs_filters ($jaut) {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $dat = true;
            $atb = true;
            $search = false;
            if(!isset($_POST["jaut-mekl"]) || $_POST["jaut-mekl"] == "" ||
                stripos($jaut->jaut, $_POST["jaut-mekl"]) !== false)
                $search = true;
            if(isset($_POST["datums"]))
                if($_POST["datums"] != "NULL")
                    if($_POST["datums"] != $jaut->datums)
                        $dat = false;
            if(isset($_POST["atbildets"])) {
                if($_POST["atbildets"] != "NULL") {
                    $atbTxt = ($jaut->atb) ? "Jā" : "Nē";
                    if($_POST["atbildets"] != $atbTxt)
                        $atb = false;
                }
            }
            return ($dat && $atb && $search);
        }
        return true;
    }
    function pjs_question_filters ($db_data, $url) {
        ?>
			<form method="POST" action="<?php echo $url; ?>">
				<button type="submit">Meklēt</button>
				<input type="text" placeholder="Meklēt pēc jautājuma nosaukuma..." name="jaut-mekl" 
				value="<?php pjs_get_on_request("jaut-mekl"); ?>"
				style="width: 300px;">
                
                <select name="datums" id="datums">
                    <option value="NULL">...</option>
                    <?php
                    $dat = array();
                    foreach ($db_data as $question) {
                        if(!in_array($question->datums, $dat)) {
                            array_push($dat, $question->datums); 
                            ?>
                            <option value="<?php echo $question->datums; ?>" 
                            <?php echo (pjs_get_on_request("datums") == $question->datums) ? "selected" : ""; ?>>
                                <?php echo $question->datums ?>
                            </option>
                            <?php 
                        }
                    }
                ?>
                </select>
                <select name="atbildets" id="atbildets">
                    <option value="NULL">...</option>
                    <option value="Jā" <?php echo (pjs_get_on_request("atbildets") == 'Jā') ? "selected" : ""; ?>>
                        Jā
                    </option>
                    <option value="Nē" <?php echo (pjs_get_on_request("atbildets") == 'Nē') ? "selected" : ""; ?>>
                        Nē
                    </option>
                </select> 
			</form> <br>
		<?php
    }
?>