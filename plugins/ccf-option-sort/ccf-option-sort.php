<?php
/*
	Plugin Name: CCF Option Sort
	Plugin URI: http://www.technorealism.com/category/wordpress-plugins/
	Description: The must have plug-in for Custom Contact Forms, sort options for form fields, dropdowns, radios, via drag and drop.
	Version: 4.7.0.8
	Author: TechnoRealism, LLC.
	Author URI: http://www.technorealism.com
*/
add_action('plugins_loaded', array('ccf_optionsort', 'ccf_check'));
add_action('admin_menu', array('ccf_optionsort', 'admin_menu'), 11);



if (!class_exists('ccf_optionsort')) {
	class ccf_optionsort {
	
public static function ccf_check() {
    $plugins = get_option('active_plugins');
    $required_plugin = 'custom-contact-forms/custom-contact-forms.php';
    
    if ( in_array( $required_plugin , $plugins ) ) {
    	self::setup();
		}else{
			add_action( 'admin_notices', array( __CLASS__, 'no_ccf_warning' ) );
	}
}
	public static function setup() {
	add_shortcode('ccf_display_forms', array(__CLASS__, 'ccf_shortcode'));
    add_action('admin_print_styles', array(__CLASS__, 'insertAdminStyles'), 1);
    add_action('admin_enqueue_scripts', array(__CLASS__, 'insertAdminScripts'), 1);
   
  	}

	public static function no_ccf_warning() {
		echo '<div class="error"><p>Custom Contact Form Sort Options plugin requires Custom Contact Forms.</p></div>';
	} 
     
public static function admin_menu(){
    //Add sub menu to CCF main menu.
    add_submenu_page('custom-contact-forms','Sort Field Options', 'Sort Field Options', 'manage_options', 'ccf_optionsort',  array(__CLASS__, 'printFieldSortPage'));				  
    add_submenu_page('custom-contact-forms','Sort Form Fields', 'Sort Form Fields', 'manage_options', 'ccf_formsort',  array(__CLASS__, 'printFormFieldSortPage'));				  
}    

public static function ccf_shortcode(){
  //Utility to browse all forms via a page or post. Like a forms library.
  $ccf_forms = CustomContactFormsDB::selectAllForms();
  $output = "<form class=\"ccf-edit-ajax\" method=\"post\" action=\"".$_SERVER['REQUEST_URI']."\"><select name=\"form\">"; 
  foreach ($ccf_forms as $ccf_form){
      $output.="<option value=\"".$ccf_form->id."\">".$ccf_form->form_title."</option>";  
  }
  $output.="</select><input type=\"submit\" name=\"load_form\" value=\"View Form\"></form>";
  if($_POST['load_form']){                                                 
     $short_code = "[customcontact form=".$_POST['form']."]";
     $output.=do_shortcode($short_code);
  }
  return $output;
}


static function insertAdminStyles() {  
	wp_register_style('ccf-optionsort', plugins_url() . '/ccf-option-sort/css/ccf_optionsort.css');
	wp_enqueue_style('ccf-optionsort');
}

static function insertAdminScripts() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-sortable');
  wp_register_script('ccf-optionsort-js', plugins_url() . '/ccf-option-sort/js/ccf_optionsort.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
  wp_enqueue_script('ccf-optionsort-js');
}
		
static function printFieldSortPage(){
  global $wpdb;
  if($_POST['active_fields']) {
    $message = '';
    $active_fields = explode(',',$_POST['active_fields']);
    foreach($active_fields as $id) {
      $sql = "UPDATE ".CCF_FIELDS_TABLE." SET field_options = '".serialize(explode(',', $_POST[$id]))."' WHERE field_slug = '".$id."'";
      $message = $message."Updated Option Order: ".$id."<br/>";
      $wpdb->query($sql);	    
    }
  }
	
//get all fields, filters below for Dropdowns and Radios    
$fields = CustomContactFormsDB::selectAllFields();
    
if (!empty($message)) { ?>
  <div id="message" class="updated below-h2">
  <p><?php echo $message; ?></p>
  </div>
<?php } ?>
<h3 class="hndle"><span>Custom Contact Forms: Order Drop Down and Radio Options</span></h3>
<form class="ccf-edit-ajax" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="widefat post" id="form-submissions-table" cellspacing="0">
<thead>
  <tr>
	<th scope="col" class="manage-column ccf-width250">Field Slug</th>
	<th scope="col" class="manage-column ccf-width150">Options</th>
  </tr>
</thead>
<tbody>
<?php
$active_fields = array();
$i = 0;
foreach ($fields as $field) {
  if (($field->field_type == 'Dropdown' || $field->field_type == 'Radio') && $field->user_field == 1 ){
    ?><tr class="row-form_submission-<?php echo $data_object->id; ?> submission-top <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
    <td><?php
    $active_fields[] = $field->field_slug;
    echo $field->field_label;
    $options = unserialize($field->field_options);
    ?></td>
    <td>
    <ul class="sortable" id="<?php echo $field->field_slug;?>"><?php 
       foreach ($options as $option){?>  
    	<li class="ui-state-default" id="<?php echo $option;?>"><?php 
      $option_meta = CustomContactFormsDB::selectFieldOption($option);
      echo $option_meta->option_label;?></li>
      <?php }?>
    </ul><input type="hidden" value="<?php echo implode(',',$options);?>" name="<?php echo $field->field_slug;?>" id="input_<?php echo $field->field_slug;?>"></td><?php 
		$i++;
    }
  }
  echo "</tr></tbody></table><input type='submit' value='Save Changes' name='Submit'><input type='hidden' value='".implode(',',$active_fields)."' name='active_fields'></form>";
}



public static function printFormFieldSortPage(){
  global $wpdb;
  if($_POST['active_forms']) {
    $message = '';
    $active_forms = explode(',',$_POST['active_forms']);
    foreach($active_forms as $id) {
      $sql = "UPDATE ".CCF_FORMS_TABLE." SET form_fields = '".serialize(explode(',', $_POST[$id]))."' WHERE form_slug = '".$id."'";
      $message = $message."Updated Field Order: ".$id."<br/>";
      $wpdb->query($sql);	    
    }
  }
//get all forms    
$forms = CustomContactFormsDB::selectAllForms();
   	
    
if (!empty($message)) { ?>
  <div id="message" class="updated below-h2">
  <p><?php echo $message; ?></p>
  </div>
<?php } ?>
<h3 class="hndle"><span>Custom Contact Forms: Order Form Fields</span></h3>
<form class="ccf-edit-ajax" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="widefat post" id="form-submissions-table" cellspacing="0">
<thead>
  <tr>
	<th scope="col" class="manage-column ccf-width250">Form Slug</th>
	<th scope="col" class="manage-column ccf-width150">Fields</th>
  </tr>
</thead>
<tbody>
<?php
$active_forms = array();
$i = 0;
foreach ($forms as $form) {
    ?><tr class="row-form_submission-submission-top <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
    <td><?php
    $active_forms[] = $form->form_slug;
    echo $form->form_title;
    $fields = unserialize($form->form_fields);
    
    ?></td>
    <td>
    <ul class="sortable" id="<?php echo $form->form_slug;?>"><?php 
       foreach ($fields as $field){?>  
    	<li class="ui-state-default" id="<?php echo $field;?>"><?php 
    	echo $field->id;
      $field_meta = CustomContactFormsDB::selectField($field);
      echo $field_meta->field_label;?> (<?php echo $field_meta->field_type;?>)</li>
      <?php }?>
    </ul><input type="hidden" value="<?php echo implode(',',$fields);?>" name="<?php echo $form->form_slug;?>" id="input_<?php echo $form->form_slug;?>"></td><?php 
		$i++;
    }
  echo "</tr></tbody></table><input type='submit' value='Save Changes' name='Submit'><input type='hidden' value='".implode(',',$active_forms)."' name='active_forms'></form>";
}
}
}