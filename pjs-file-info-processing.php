<?php
    function pjs_if_allowed ($wpdb) {
        foreach ($_FILES["fails"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $file_name = $_FILES['fails']['name'][$key];
                $file_size = $_FILES['fails']['size'][$key];
                $file_error = $_FILES['fails']['error'][$key];
                $file_ext = explode('.', $file_name);
                $file_act_ext = strtolower(end($file_ext));
                $allowed = array("jpg", "png", "doc", "pdf", "docx");
                if(!in_array($file_act_ext, $allowed)){
                    echo "Neatļauti failu tipi: " . $file_name;
                    return false;
                }
                if($file_error != 0){
                    echo "Kļūda augšupielādējot failu/-us: " . $file_name;
                    return false;
                }
                if( $file_size > 5000000 ){
                    echo " Pārsniegts maksimālais pieļaujamais failu izmērs 5 MB: " . $file_name;
                    return false;
                }
            } 
        }
        return true;
    }
    function pjs_save_files ($wpdb, $ent_type, $ent_id) {
        if(file_exists($_FILES['fails']['tmp_name'][0])) {
            if(is_countable ($_FILES['fails']['name'])) {
                $count = count($_FILES['fails']['name']);
                if ($count > 0) {
                    if (pjs_if_allowed($wpdb)) {
                        $path = plugin_dir_path(__FILE__)."files/".$ent_type.'_'.$ent_id.'/';
                        $url = plugin_dir_url(__FILE__)."files/".$ent_type.'_'.$ent_id.'/';
                        mkdir($path);
                        foreach ($_FILES["fails"]["error"] as $key => $error) {
                            $file_name = $_FILES['fails']['name'][$key];
                            $file_tmp = $_FILES['fails']['tmp_name'][$key];
                            $fin_path = $path . $file_name;
                            $wpdb->insert(
                                $wpdb->prefix . 'pjs_files',
                                array(
                                    'ent_type' => $ent_type,
                                    'ent_id' => $ent_id,
                                    'path' => $url . $file_name
                                ) 
                            );
                            if(!move_uploaded_file($file_tmp, $fin_path)) {
                                echo "Kļūda augšupielādējot failu/-us. Failu augšupielādēt neizdevās.";
                                return;
                            }
                        }
                    }
                }
            }
        }
    }
    function pjs_load_file_links ($ent_id, $type, $removable = false) {
        global $wpdb;
        $file_table_name = $wpdb->prefix . 'pjs_files';
        $file_data = $wpdb->get_results(
            "SELECT * FROM $file_table_name WHERE ent_id = $ent_id AND ent_type = '$type'"
        );
        if(!empty($file_data)) {
            foreach ($file_data as $file) {
                $file_id = $file->id;
                $file_name = basename($file->path);
                if($removable) {
                    if(isset($_POST['pjs_file_delete' . $file_id])) {
                        wpdb->delete(
                            $file_table_name,
                            array (
                                'id' => $file_id
                            )
                        );
                    }
                    ?> <input type="submit" name="pjs_file_delete <?php echo '_'.$file_id; ?>"> <?php
                }
                ?> <a href="<?php echo $file->path; ?>"><?php echo $file_name; ?></a><br> <?php
            }
        } else echo "<p>Pagaidām nav</p>";
    }
?>