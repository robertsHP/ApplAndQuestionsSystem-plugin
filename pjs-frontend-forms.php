<?php
    function pjs_get_question_frontend_form () {
        ob_start();
        ?>
        <form action="<?php echo get_permalink() ?>" method="post" enctype="multipart/form-data">

            <!-- Pieteicējs -->
            <label for="vards">Pieteicējs:</label><br>
            <input type="text" id="vards" name="vards" 
            value="<?php echo (is_user_logged_in()) ? wp_get_current_user()->display_name : "Nereģistrēts lietotājs"; ?>"
            required readonly>
            <br><br>
            
            <!-- E-pasts -->
            <label for="epasts">E-pasts:</label><br>
            <input type="email" id="epasts" name="epasts" 
            value="<?php echo isset($_POST["epasts"]) ? $_POST["epasts"] : ''; ?>" required>
            <br><br>

            <!-- Jautājums -->
            <textarea class="textarea" name="jautajums" id="jautajums" rows="10" cols="30" minlength="10"
            placeholder="Brīvā formā uzrakstiet savu jautājumu!" required>
            <?php if(isset($_POST['jautajums'])) {echo htmlentities ($_POST['jautajums']); }?>
            </textarea>
            <br><br>
            
            <!-- Fails -->
            <label for="fails">Augšupielādēt failu (Max. 5 MB):</label><br>
            <label for="fails">Atļautie failu tipi: .jpg; .png; .doc; .pdf</label><br>
            <input type="file" id="fails" name="fails[]" multiple>
            <br><br>
            
            <!-- Poga -->
            <input type="submit" name="pjs_question_submit" value="Nosūtīt">
            <br><br>
            
        </form>
        <?php
        return ob_get_clean();
    }
    function pjs_question_frontend_form_submit () {
        global $wpdb;
        require_once(plugin_dir_path(__FILE__) . 'pjs-file-info-processing.php');
        if(isset($_POST['pjs_question_submit'])) {
            $vards = sanitize_text_field($_POST['vards']);
            $epasts = sanitize_text_field($_POST['epasts']);
            $jautajums = sanitize_text_field($_POST['jautajums']);

            if (strlen($jautajums) < 10) {
                echo "Lūdzu, precizējiet veicamā jautājuma aprakstu!" . "<br> <br>";
                return;
            }
            $result = $wpdb->insert( 
                $wpdb->prefix . 'pjs_questions',
                array( 
                    'jaut' => $jautajums,
                    'pieteicejs' => $vards,
                    'epasts' => $epasts,
                    'datums' => date('Y-m-d H:i:s'),
                    'sniegta_atb' => false
                )
            );
            if ($result == false) {
                echo "Kļūda: nosūtot lietotāju datus uz DB.";
                return;
            }
            pjs_save_files($wpdb, 'pjs_questions', $wpdb->insert_id);
        }
    }
    function pjs_get_job_appl_frontend_form () {
        ob_start();
        ?>
        <form action="<?php echo get_permalink() ?>" method="post" enctype="multipart/form-data">

            <!-- Pieteicējs -->
            <label for="vards">Pieteicējs:</label><br>
            <input type="text" id="vards" name="vards" 
            value="<?php echo (is_user_logged_in()) ? wp_get_current_user()->display_name : "Nereģistrēts lietotājs"; ?>"
            required readonly>
            <br><br>
            
            <!-- E-pasts -->
            <label for="epasts">E-pasts:</label><br>
            <input type="email" id="epasts" name="epasts" 
            value="<?php echo isset($_POST["epasts"]) ? $_POST["epasts"] : ''; ?>" required>
            <br><br>
            
            <!-- Darba nosaukums -->
            <label for="darbs">Veicamais darbs:</label><br>
            <input type="text" name="darbs" id="darbs" minlength="10" 
            value="<?php if(isset($_POST['darbs'])) {echo htmlentities ($_POST['darbs']); }?>" required>
            <br><br>
            
            <!-- Darba apraksts -->
            <label for="apraksts">Veicamā darba apraksts:</label><br>
            <textarea class="textarea" name="apraksts" id="apraksts" rows="10" cols="30" minlength="30"
            required>
            <?php echo (isset($_POST['apraksts'])) ? htmlentities($_POST['apraksts']) : ''; ?>
            </textarea>
            <br><br>  

            <!-- Pielikuma fails -->
            <label for="fails">Augšupielādēt failu (Max. 5 MB):</label><br>
            <label for="fails">Atļautie failu tipi: .jpg; .png; .doc; .pdf</label><br>
            <input type="file" id="fails" name="fails[]" multiple>
            <br><br>
            
            <!-- Poga -->
            <input type="submit" name="pjs_job_appl_submit" value="Nosūtīt">
            <br><br>
            
        </form>
        <?php
        return ob_get_clean();
    }
    function pjs_job_appl_frontend_form_submit () {
        global $wpdb;
        require_once(plugin_dir_path(__FILE__) . 'pjs-file-info-processing.php');
        if(isset($_POST['pjs_job_appl_submit'])) {
            $vards = sanitize_text_field($_POST['vards']);
            $epasts = sanitize_text_field($_POST['epasts']);
            $darbs = sanitize_text_field($_POST['darbs']);
            $apraksts = sanitize_text_field($_POST['apraksts']);
            
            if (strlen($darbs) < 10) {
                echo "Lūdzu, precizējiet veicamo darbu!" . "<br> <br>";
                return;
            }

            if (strlen($apraksts) < 30) {
                echo "Lūdzu, precizējiet veicamā darba aprakstu!" . "<br> <br>";
                return;
            }
            $result = $wpdb->insert( 
                $wpdb->prefix . 'pjs_job_appls',
                array( 
                    'veic_darbs' => $darbs,
                    'apraksts' => $apraksts,
                    'pieteicejs' => $vards,
                    'epasts' => $epasts,
                    'statuss' => "Pieteikts"
                )
            );
            if ($result == false) {
                echo "Kļūda: nosūtot lietotāju datus uz DB.";
                return;
            }
            pjs_save_files($wpdb, 'pjs_job_appls', $wpdb->insert_id);
        }
    }
?>