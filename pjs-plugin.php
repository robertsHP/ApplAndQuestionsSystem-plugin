<?php
    /*
        Plugin Name: Pieteikumu un jautājumu sistēmas plugin
        Description: 
        Version: 1.0
    */

    require_once(plugin_dir_path(__FILE__) . 'pjs-frontend-forms.php');

    global $pjs_db_version;
    $pjs_db_version = "1.0";

    //REMOVE LATER
    function pjs_print ($str) {
        $postlog = get_stylesheet_directory() . 'logs.txt';
        if(file_exists($postlog))
            $file = fopen($postlog, 'a');
        else $file = fopen($postlog, 'w');
        fwrite($file, $str . "\n");
        fclose($file);
    }

////////Datu bāzu tabulas

    function pjs_create_tables($wpdb) {
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'pjs_questions';
        $sql1 = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            jaut LONGTEXT NOT NULL,
            pieteicejs VARCHAR(40) NOT NULL,
            epasts VARCHAR(100) NOT NULL,
            datums DATE NOT NULL,
            atb_datums DATE NOT NULL,
            atb LONGTEXT NOT NULL,
            sniegta_atb BOOLEAN NOT NULL
            PRIMARY KEY (id)
        ) $charset_collate;";
        $table_name = $wpdb->prefix . 'pjs_job_appls';
        $sql2 = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            veic_darbs TEXT NOT NULL,
            apraksts LONGTEXT,
            pieteicejs VARCHAR(40) NOT NULL,
            epasts VARCHAR(100) NOT NULL,
            statuss VARCHAR(30) NOT NULL,
            sak_dat DATE NOT NULL,
            beigu_dat DATE NOT NULL,
            prio VARCHAR(30) NOT NULL,
            admin_kom LONGTEXT,
            darb_veic_kom LONGTEXT
            PRIMARY KEY (id)
        ) $charset_collate;";
        $table_name = $wpdb->prefix . 'pjs_files';
        $sql3 = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            ent_type VARCHAR(30) NOT NULL,
            ent_id BIGINT(20) NOT NULL,
            path VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        $table_name = $wpdb->prefix . 'pjs_faq';
        $sql4 = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            jaut TEXT NOT NULL,
            atb TEXT NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        $table_name = $wpdb->prefix . 'pjs_workers_empl';
        $sql5 = "CREATE TABLE $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            job_appl_id BIGINT(20) NOT NULL,
            worker VARCHAR(30) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
        dbDelta($sql4);
        dbDelta($sql5);
        pjs_add_data_to_DB($wpdb);
    }
    function pjs_add_data_to_DB ($wpdb) {
        $table_name = $wpdb->prefix . 'pjs_faq';
        $wpdb->insert($table_name, array(
            'jaut' => 'Lai izmantotu sistēmu, vai ir nepieciešama reģistrācija?', 
            'atb' => 'Nē, sistēmu var izmantot arī nereģistrēti lietotāji.'));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kādas priekšrocības ir reģistrētiem lietotājiem?', 
            'atb' => ''));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kā uzdot jautājumu?', 
            'atb' => 'Navigācijas panelī jāizvēlas sadaļa “Jautājumu pieteikšana”, kur ir jāizpilda un jānosūta forma.'));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kā pieteikt darbu?', 
            'atb' => 'Navigācijas panelī jāizvēlas sadaļa “Darbu pieteikšana”, kur ir jāizpilda un jānosūta forma.'));
        $wpdb->insert($table_name, array(
            'jaut' => 'Cik ilgā laikā tiek apstrādāts jautājums/darba pieteikums?', 
            'atb' => '3 darba dienu laikā.'));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kā es uzzināšu, ka uz manu jautājumu ir pienākusi atbilde?', 
            'atb' => ''));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kā es varu uzzināt ar sevis pieteikto darbu sasitīto informāciju?', 
            'atb' => ''));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kā es varu izveidot savu kontu?', 
            'atb' => ''));
        $wpdb->insert($table_name, array(
            'jaut' => 'Kā es varu izdzēst savu kontu?', 
            'atb' => ''));
    }
    register_activation_hook( __FILE__, 'pjs_update_tables');
    function pjs_update_tables () {
        global $pjs_db_version;
        global $wpdb;
        $version = get_option("pjs_db_version");
        pjs_create_tables($wpdb);
        if(!$version)
            add_option('pjs_db_version', $pjs_db_version);
        else update_option("pjs_db_version", $pjs_db_version);
    }
    add_action('plugins_loaded', 'pjs_check_db_updates');
    function pjs_check_db_updates() {
        global $pjs_db_version;
        global $wpdb;
        if (get_site_option('pjs_db_version') != $pjs_db_version) {
            pjs_create_tables($wpdb);
        }
    }

    add_action('admin_enqueue_scripts', 'pjs_load_styles');
    function pjs_load_styles($hook) {
        $current_screen = get_current_screen();
        if(strpos($current_screen->base, 'pjs-plugin') === false) {
            return;
        } else {
            wp_enqueue_style('custom_wp_admin_css', plugins_url('/admin-style.css', __FILE__));
        }
    }

    add_action('admin_menu', 'pjs_add_question_menu');
    function pjs_add_question_menu() {
        add_menu_page(
            'Jautajumi',
            'Jautajumi',
            'questions',
            'pjs-plugin/pjs-question-page.php'
        );
    }
    add_shortcode('pjs_question', 'pjs_question_shortcode');
    function pjs_question_shortcode($atts = [], $content = null) {
        $content = pjs_get_question_frontend_form();
        return $content;
    }
    add_action('wp_head', 'pjs_question_frontend_form_submit');

    add_action('admin_menu', 'pjs_add_job_appl_menu');
    function pjs_add_job_appl_menu() {
        add_menu_page(
            'Darba pieteikumi',
            'Darba pieteikumi',
            'job_applications',
            'pjs-plugin/pjs-job-appl-page.php'
        );
    }
    add_shortcode('pjs_job_appl', 'pjs_job_appl_shortcode');
    function pjs_job_appl_shortcode($atts = [], $content = null) {
        $content = pjs_get_job_appl_frontend_form();
        return $content;
    }
    add_action('wp_head', 'pjs_job_appl_frontend_form_submit');

    add_action('admin_menu', 'pjs_add_all_appl_menu');
    function pjs_add_all_appl_menu() {
        add_menu_page(
            'Visi pieteikumi',
            'Visi pieteikumi',
            'all_appl',
            'pjs-plugin/pjs-all-appl-page.php'
        );
    }

    add_action('admin_menu', 'pjs_perf_menu');
    function pjs_perf_menu() {
        add_menu_page(
            'Piešķirtie uzdevumi',
            'Piešķirtie uzdevumi',
            'job_perf',
            'pjs-plugin/pjs-job-perf-page.php'
        );
    }
?>