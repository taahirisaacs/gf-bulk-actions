<?php
GFForms::include_addon_framework();

class GFBulkActionsProAddOn extends GFAddOn {

	protected $_version = GF_BULK_ACTIONS_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'bulk-actions-pro-for-gravity-forms';
	protected $_path = 'bulk-actions-pro-for-gravity-forms/bulk-actions-pro-for-gravity-forms.php';
	protected $_full_path = __FILE__;
	protected $_title = GF_BULK_ACTIONS_NAME;
	protected $_short_title = 'Bulk Actions PRO';
	protected $_website = 'http://jetsloth.com/bulk-actions-for-gravity-forms/';

	private static $_instance = null;

	public function __construct() {
		$this->_capabilities_plugin_page = 'gravityforms_edit_forms';
		$this->_capabilities_settings_page = 'gravityforms_edit_settings';
		parent::__construct();
	}

	/**
	 * Get an instance of this class.
	 * @return GFBulkActionsProAddOn
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFBulkActionsProAddOn();
		}
		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
	}


	public function init_admin() {
		parent::init_admin();
		add_filter( 'gform_toolbar_menu', array( $this, 'gf_bulk_actions_toolbar_item' ), 10, 2 );
		add_filter( 'gform_form_actions', array( $this, 'gf_bulk_actions_quick_link' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'handle_bulk_actions_submit' ), 10, 2 );

		$name = plugin_basename($this->_path);
		add_action( 'after_plugin_row_'.$name, array( $this, 'gf_plugin_row' ), 10, 2 );
	}


	public function init_ajax() {
		parent::init_admin();
		add_action('wp_ajax_gf_bulk_actions_copy_fields', array($this, 'ajax_copy_fields'));
	}

	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'gf_bulk_actions_js',
				'src'     => $this->get_base_url() . '/js/gf-bulk-actions-pro.js',
				'version' => $this->_version,
				'deps'    => array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-sortable', 'thickbox'),
				'strings' => array(
					'selfUrl' => self_admin_url('admin.php?page='.$this->_slug.'&gform_id='),
					'saveReminderTitle' => esc_html__('Note', 'gf_bulk_actions_pro'),
					'saveReminderText' => esc_html__("Remember to click Update Form to save your edits.<br/><br/><label><input type=\"checkbox\" name=\"hide_save_reminder\" value=\"1\" /> <span>Don't remind me next time.</span></label>", 'gf_bulk_actions_pro'),
					'saveReminderConfirmText' => esc_html__("Ok", 'gf_bulk_actions_pro'),
					'warnDeleteAllTitle' => esc_html__('Wait!', 'gf_bulk_actions_pro'),
					'warnDeleteAllText' => esc_html__("You can't delete every field in a form.", 'gf_bulk_actions_pro'),
					'warnDeleteConfirmText' => esc_html__("Ok", 'gf_bulk_actions_pro'),
					'duplicateReminderTitle' => esc_html__('', 'gf_bulk_actions'),
					'duplicateReminderConfirmText' => esc_html__("Duplicate", 'gf_bulk_actions'),
					'confirmCopyUnsavedTitle' => esc_html__('Wait!', 'gf_bulk_actions_pro'),
					'confirmCopyUnsavedText' => esc_html__("One or more of the fields you've selected have been edited <br/>or are new (duplicates).<br/><br/>If you copy these before saving the form only original unedited fields <br/>will be copied, and new duplicates won't be copied at all.", 'gf_bulk_actions_pro'),
					'confirmCopyUnsavedConfirmText' => esc_html__('Continue anyway', 'gf_bulk_actions_pro'),
					'confirmCopyUnsavedCancelText' => esc_html__('Cancel', 'gf_bulk_actions_pro'),
					'confirmCopyTitle' => esc_html__('Copy # field(s) to:', 'gf_bulk_actions_pro'),
					'confirmCopyConfirmText' => esc_html__('Copy', 'gf_bulk_actions_pro'),
					'confirmCopyCancelText' => esc_html__('Cancel', 'gf_bulk_actions_pro'),
					'successCopyTitle' => esc_html__('Done!', 'gf_bulk_actions_pro'),
					'successCopyText' => esc_html__('# field(s) successfully copied to @stotal@ forms', 'gf_bulk_actions_pro'),
					'successCopyWithErrorsText' => esc_html__('# field(s) successfully copied to @stotal@ of @ftotal@ forms.', 'gf_bulk_actions_pro'),
					'successCopyWithErrorsList' => esc_html__('Copy to the following forms failed:', 'gf_bulk_actions_pro'),
					'successCopyConfirmText' => esc_html__('Ok', 'gf_bulk_actions_pro'),
					'errorCopyTitle' => esc_html__('Oops!', 'gf_bulk_actions_pro'),
					'errorCopyText' => esc_html__('Error copying field(s). Please try again.', 'gf_bulk_actions_pro'),
					'errorCopyConfirmText' => esc_html__('Ok', 'gf_bulk_actions_pro'),
					'confirmCloneTitle' => esc_html__('Clone selected field(s):', 'gf_bulk_actions_pro'),
					'confirmCloneConfirmText' => esc_html__('Clone', 'gf_bulk_actions_pro'),
					'confirmCloneCancelText' => esc_html__('Cancel', 'gf_bulk_actions_pro'),
					'confirmCloneText' => esc_html__('Enter the number of clones to create:<br/>', 'gf_bulk_actions_pro'),
					'confirmResetTitle' => esc_html__('Confirm reset', 'gf_bulk_actions_pro'),
					'confirmReset' => esc_html__('You have unsaved changes. Are you sure want to reset?', 'gf_bulk_actions_pro'),
					'confirmResetConfirmText' => esc_html__('Reset', 'gf_bulk_actions_pro'),
					'confirmResetCancelText' => esc_html__('Cancel', 'gf_bulk_actions_pro'),
					'confirmDeleteTitle' => esc_html__('Confirm delete', 'gf_bulk_actions_pro'),
					'confirmDelete' => esc_html__('Are you sure want to delete the selected field(s)?<br/>Deleting fields will also delete all entry data associated with them.', 'gf_bulk_actions_pro'),
					'confirmDeleteConfirmText' => esc_html__('Delete fields', 'gf_bulk_actions_pro'),
					'confirmDeleteCancelText' => esc_html__('Cancel', 'gf_bulk_actions_pro'),
					'confirmLeaveTitle' => esc_html__('Are you sure?', 'gf_bulk_actions_pro'),
					'confirmLeave' => esc_html__('You have unsaved changes that will be lost if you navigate away from this page.', 'gf_bulk_actions_pro'),
					'confirmLeaveConfirmText' => esc_html__('Leave this page', 'gf_bulk_actions_pro'),
					'confirmLeaveCancelText' => esc_html__('Stay on this page', 'gf_bulk_actions_pro'),
					'confirmPreviewTitle' => esc_html__('Are you sure?', 'gf_bulk_actions_pro'),
					'confirmPreview' => esc_html__('You have unsaved changes. The preview will not show these.', 'gf_bulk_actions_pro'),
					'confirmPreviewConfirmText' => esc_html__('Preview without changes', 'gf_bulk_actions_pro'),
					'confirmPreviewCancelText' => esc_html__('Cancel', 'gf_bulk_actions_pro')
				),
				'enqueue' => array(
					array(
						'admin_page' => array('plugin_page')
					)
				)
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {
		$styles = array(
			array(
				'handle'  => 'gf_bulk_actions_css',
				'src'     => $this->get_base_url() . '/css/gf-bulk-actions.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array('plugin_page', 'form_editor', 'form_settings', 'entry_view', 'entry_detail', 'results')
					)
				)
			)
		);

		return array_merge( parent::styles(), $styles );
	}


	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Creates a settings page for this add-on.
	 */
	public function plugin_settings_fields() {

		$license = $this->get_plugin_setting('gf_bulk_actions_pro_license_key');
		$status = get_option('gf_bulk_actions_pro_license_status');

		$license_field = array(
			'name' => 'gf_bulk_actions_pro_license_key',
			'tooltip' => esc_html__('Enter the license key you received after purchasing the plugin.', 'gf_bulk_actions_pro'),
			'label' => esc_html__('License Key', 'gf_bulk_actions_pro'),
			'type' => 'text',
			'input_type' => 'password',
			'class' => 'medium',
			'default_value' => '',
			'validation_callback' => array($this, 'license_validation'),
			'feedback_callback' => array($this, 'license_feedback'),
			'error_message' => esc_html__( 'Invalid license', 'gf_bulk_actions_pro' ),
		);

		if (!empty($license) && !empty($status)) {
			$license_field['after_input'] = ($status == 'valid') ? ' License is valid' : ' Invalid or expired license';
		}

		$fields = array(
			array(
				'title'  => esc_html__('To unlock plugin updates, please enter your license key below', 'gf_bulk_actions_pro'),
				'fields' => array(
					$license_field
				)
			)
		);

		return $fields;
	}

	/**
	 * Creates a custom page for this add-on.
	 */
	public function plugin_page() {

		if (isset($_REQUEST['result']) && $_REQUEST['result'] == 'error') { ?>
			<div class="error notice">
				<p><?php _e('Error saving form.', 'gf_bulk_actions_pro'); ?></p>
			</div>
		<?php } elseif (isset($_REQUEST['result']) && $_REQUEST['result'] == 'updated') { ?>
			<div class="updated notice">
				<p><?php _e('Form updated successfully', 'gf_bulk_actions_pro'); ?></p>
			</div>
		<?php }

?>
			<form id="gf_bulk_actions_form_select" method="get" action="<?php echo self_admin_url('admin.php?page='.$this->_slug); ?>" novalidate="novalidate">
				<input type="hidden" name="page" value="<?php echo $this->_slug; ?>" />
				<table id="form_select" class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row"><label for="gform_select"><?php _e('Form', 'gf_bulk_actions_pro'); ?>:</label></th>
						<td>
							<select name="gform_id" id="gform_select">
								<?php
								$forms = GFAPI::get_forms();
								$selected_id = (isset($_REQUEST['gform_id']) && !empty($_REQUEST['gform_id'])) ? $_REQUEST['gform_id'] : $forms[0]['id'];
								$selected_form = NULL;
								foreach($forms as $form): ?>
								<option <?php if ($form['id'] == $selected_id) { $selected_form = $form; echo 'selected="selected"'; } ?> value="<?php echo $form['id']; ?>"><?php echo $form['title']; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td align="right">
							<a href="<?php echo self_admin_url('admin.php?page=gf_edit_forms&id='.$selected_form['id']); ?>" class="button button-secondary button-large" id="return_form"><?php _e('Form Editor', 'gf_bulk_actions_pro'); ?></a>&nbsp;
							<a href="<?php echo site_url('/?gf_page=preview&id='.$selected_form['id']); ?>" class="button button-secondary button-large" id="preview_form" target="_blank"><?php _e('Preview Form', 'gf_bulk_actions_pro'); ?></a> &nbsp;
							<button type="button" class="button button-primary button-large" id="update_form"><?php _e('Update Form', 'gf_bulk_actions_pro'); ?></button>
						</td>
					</tr>
					</tbody>
				</table>
			</form>
			<form id="gf_bulk_actions_form_edit" method="post" action="<?php echo self_admin_url('admin.php?page='.$this->_slug); ?>" novalidate="novalidate">
				<input type="hidden" name="page" value="<?php echo $this->_slug; ?>" />
				<input type="hidden" name="bulk_edit_action" value="update" />
				<input type="hidden" name="gform_id" value="<?php echo $selected_form['id']; ?>" />
				<input type="hidden" name="updated_fields" value="" />
			</form>
			<div id="actions">
				<ul class="subsubsub" id="selection_options">
					<li><?php _e('Select', 'gf_bulk_actions_pro'); ?>: </li>
					<li><a href="javascript:void(0);" id="select_all"><?php _e('All', 'gf_bulk_actions_pro'); ?></a> |</li>
					<li><a href="javascript:void(0);" id="select_invert"><?php _e('Invert', 'gf_bulk_actions_pro'); ?></a> |</li>
					<li><a href="javascript:void(0);" id="select_none"><?php _e('None', 'gf_bulk_actions_pro'); ?></a></li>
				</ul>
				<ul class="subsubsub" id="action_options">
					<li><button type="button" id="edit_fields" class="button button-secondary button-large" disabled="disabled"><span class="dashicons dashicons-edit"></span> <span><?php _e('Edit', 'gf_bulk_actions_pro'); ?></span></button></li>
					<li><button type="button" id="duplicate_fields" class="button button-secondary button-large" disabled="disabled"><span class="dashicons dashicons-welcome-add-page"></span> <span><?php _e('Duplicate', 'gf_bulk_actions_pro'); ?></span></button></li>
					<li><button type="button" id="clone_fields" class="button button-secondary button-large" disabled="disabled"><span class="dashicons dashicons-admin-page"></span> <span><?php _e('Clone', 'gf_bulk_actions_pro'); ?></span></button></li>
					<li><button type="button" id="copy_fields" class="button button-secondary button-large" disabled="disabled"><span class="dashicons dashicons-redo"></span> <span><?php _e('Copy to form', 'gf_bulk_actions_pro'); ?></span></button></li>
					<li><button type="button" id="delete_fields" class="button button-secondary button-large" disabled="disabled"><span class="dashicons dashicons-trash"></span> <span><?php _e('Delete', 'gf_bulk_actions_pro'); ?></span></button></li>
				</ul>
				<div id="options">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tbody>
						<tr>
							<td valign="middle">
								<i><?php _e('Use Ctrl (Cmd) + Click to select multiple fields individually, or Shift + Click to select a range', 'gf_bulk_actions_pro'); ?></i><br/>
								<i><?php _e('Double-click a field to edit individual labels. Press Enter to finish editing', 'gf_bulk_actions_pro'); ?></i>
							</td>
							<td valign="middle" align="right">
								<span><?php _e('Duplicate/clone placement', 'gf_bulk_actions_pro'); ?>:</span> <label><input type="radio" name="duplicate_placement" value="top" /> <?php _e('Top', 'gf_bulk_actions_pro'); ?></label> &nbsp; <label><input type="radio" name="duplicate_placement" value="bottom" checked="checked" /> <?php _e('Bottom', 'gf_bulk_actions_pro'); ?></label> &nbsp; <label><input type="radio" name="duplicate_placement" value="inline" /> <?php _e('Inline', 'gf_bulk_actions_pro'); ?></label>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id="form_fields_wrap">
				<script type="text/template" id="fields_copy_template">
					<form id="gf_bulk_actions_fields_copy" method="post" action="<?php echo self_admin_url('admin.php?page='.$this->_slug); ?>" novalidate="novalidate">
						<input type="hidden" name="bulk_fields_copy_source_form" id="bulk_fields_copy_source_form" value="<?php echo $selected_id; ?>" />
						<input type="hidden" name="bulk_fields_copy_fields" id="bulk_fields_copy_fields" />
						<select name="bulk_fields_copy_target_form" id="bulk_fields_copy_target_form" multiple>
							<?php
							foreach($forms as $form):
								if ($form['id'] != $selected_id): ?>
									<option value="<?php echo $form['id']; ?>"><?php echo $form['title']; ?></option>
								<?php endif;
							endforeach; ?>
						</select>
					</form>
				</script>
				<form id="gf_bulk_actions_fields_edit" method="post" action="<?php echo self_admin_url('admin.php?page='.$this->_slug); ?>" novalidate="novalidate">
					<div id="bulk_fields_edit_mode">
						<b><?php _e('Edit Mode:', 'gf_bulk_actions_pro'); ?></b> &nbsp; <label><input type="radio" name="fields_edit_mode" value="singles" checked="checked" /> <?php _e('Individual', 'gf_bulk_actions_pro'); ?></label> &nbsp; <label><input type="radio" name="fields_edit_mode" value="bulk" /> <?php _e('Bulk', 'gf_bulk_actions_pro'); ?></label>
					</div>
					<table id="bulk_fields_edit" class="form-table">
						<thead>
						<tr valign="top">
							<th style="width:20%;"><?php _e('Field Label', 'gf_bulk_actions_pro'); ?></th>
							<th style="width:30%;"><?php _e('Field Description', 'gf_bulk_actions_pro'); ?></th>
							<th style="width:20%;"><?php _e('Admin Label', 'gf_bulk_actions_pro'); ?></th>
							<th style="width:20%;"><?php _e('CSS Class', 'gf_bulk_actions_pro'); ?></th>
							<th style="width:10%; text-align:center;"><?php _e('Required', 'gf_bulk_actions_pro'); ?> <input type="checkbox" id="required_toggle_all" name="required_toggle_all" value="1" /></th>
						</tr>
						</thead>
						<tbody>
						<tr valign="top" class="bulk">
							<td>
								<input type="text" id="bulk_fields_label" name="bulk_fields_label" value="" /><br/>
								<i><?php _e('Leave empty to keep labels unchanged', 'gf_bulk_actions_pro'); ?></i>
							</td>
                            <td>
                                <textarea id="bulk_fields_description" name="bulk_fields_description"></textarea><br/>
								<i><?php _e('Leave empty to keep descriptions unchanged', 'gf_bulk_actions_pro'); ?></i>
							</td>
							<td>
								<input type="text" id="bulk_fields_admin_label" name="bulk_fields_admin_label" value="" /><br/>
								<i><?php _e('Leave empty to keep admin labels unchanged', 'gf_bulk_actions_pro'); ?></i>
							</td>
							<td>
								<input type="text" id="bulk_fields_cls" name="bulk_fields_cls" value="" /><br/>
								<i><?php _e('Custom CSS classes entered here will be appended to all fields', 'gf_bulk_actions_pro'); ?></i>
							</td>
							<td align="center">
								<select id="bulk_fields_required" name="bulk_fields_required">
									<option value="unchanged"><?php _e('Leave unchanged', 'gf_bulk_actions_pro'); ?></option>
									<option value="required"><?php _e('All required', 'gf_bulk_actions_pro'); ?></option>
									<option value="not_required"><?php _e('All not required', 'gf_bulk_actions_pro'); ?></option>
								</select><br/>
								&nbsp;
							</td>
						</tr>
						</tbody>
						<tfoot>
						<tr valign="top">
							<td>
								<i class="important"><?php _e('These settings will be applied to all selected fields.', 'gf_bulk_actions_pro'); ?></i>
							</td>
							<td colspan="4" align="right">
								<button type="button" id="cancel_bulk_fields_edits" class="button button-secondary button-large"><?php _e('Cancel', 'gf_bulk_actions_pro'); ?></button> &nbsp;
								<button type="button" id="submit_bulk_fields_edits" class="button button-primary button-large"><?php _e('Done', 'gf_bulk_actions_pro'); ?></button>
							</td>
						</tr>
						</tfoot>
					</table>
				</form>

				<ul id="form_fields" class="fields">
					<?php
					$fields = $selected_form['fields'];
					foreach($fields as $field):
						$field_title = $field['label'];
						$field_class_extra = 'field--'.$field['type'];
						if ($field['type'] == 'page') {
							$field_title = '-- Page Break --';
						}

						$field_custom_cls = (isset($field['cssClass']) && !empty($field['cssClass'])) ? $field['cssClass'] : "";

						if ($field->isRequired) {
							$field_class_extra = ' field--required';
						}
						?>
						<li class="postbox field <?php echo $field_class_extra; ?>" id="field_<?php echo $field['id']; ?>" data-custom-cls="<?php echo $field_custom_cls; ?>" data-id="<?php echo $field['id']; ?>" data-type="<?php echo $field['type']; ?>" data-admin-label="<?php echo $field['adminLabel']; ?>" data-description="<?php echo htmlspecialchars($field['description'], ENT_QUOTES, 'UTF-8'); ?>">
							<label class="field__title"><span class="field__title-text"><?php echo $field_title; ?></span><input type="text" class="field__title-input" name="field_<?php echo $field['id']; ?>_title" value="<?php echo $field_title; ?>" disabled="disabled" /> <span class="field__id">(ID: <?php echo $field['id']; ?>)</span></label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<script type="text/javascript">
				var form = <?php echo json_encode($selected_form); ?>;
			</script>
<?php
	}


	// # HELPERS -------------------------------------------------------------------------------------------------------

	/**
	 * Handle the main submit of bulk actions for a form
	 */
	public function handle_bulk_actions_submit() {

		if (isset($_POST['bulk_edit_action']) && $_POST['bulk_edit_action'] == 'update') {
			$form_id = (isset($_POST['gform_id']) && !empty($_POST['gform_id'])) ? intval($_POST['gform_id']) : NULL;
			$new_update_fields = (!empty($form_id) && isset($_POST['updated_fields']) && !empty($_POST['updated_fields'])) ? json_decode(stripslashes($_POST['updated_fields']), true) : NULL;

			if (!empty($new_update_fields)) {
				$form = GFAPI::get_form($form_id);

				$new_next_field_id = 0;
				foreach($new_update_fields as $field) {
					$id = intval($field['id']);
					if ($id > $new_next_field_id) {
						$new_next_field_id = $id;
					}
				}
				$new_next_field_id += 1;
				$form['nextFieldId'] = $new_next_field_id;

				$form['fields'] = $new_update_fields;
				$update_result = GFAPI::update_form($form);
				$result = (is_wp_error($update_result)) ? 'error' : 'updated';

				$url = self_admin_url('admin.php?page='.$this->_slug.'&gform_id='.$form_id.'&result='.$result);
				wp_redirect($url);
				exit;
			}
		}

	}


	/**
	 * Handle ajax request to copy fields to another form
	 */

	public function ajax_copy_fields() {
		if (!isset($_POST['action']) || $_POST['action'] != 'gf_bulk_actions_copy_fields') {
			die();
		}
		if (!isset($_POST['bulk_fields_copy_fields']) || empty($_POST['bulk_fields_copy_fields'])
				|| !isset($_POST['bulk_fields_copy_source_form']) || empty($_POST['bulk_fields_copy_source_form'])
				|| !isset($_POST['bulk_fields_copy_target_forms']) || empty($_POST['bulk_fields_copy_target_forms']))
		{
			die();
		}


		$target_form_ids = $_POST['bulk_fields_copy_target_forms'];
		$update_results = array();
		$update_errors = 0;

		foreach($target_form_ids as $target_form_id) {

			$target_form = GFAPI::get_form( (int) $target_form_id );

			$target_form_last_field_id = 0;
			foreach($target_form['fields'] as $field) {
				$id = intval($field->id);
				if ($id > $target_form_last_field_id) {
					$target_form_last_field_id = $id;
				}
			}


			$source_form_id = intval($_POST['bulk_fields_copy_source_form']);
			//$source_form = GFAPI::get_form($source_form_id);
			$target_form_last_field_id += 1;

			$new_copy_fields = (!empty($source_form_id) && isset($_POST['bulk_fields_copy_fields']) && !empty($_POST['bulk_fields_copy_fields'])) ? json_decode(stripslashes($_POST['bulk_fields_copy_fields']), true) : NULL;

			$new_copy_fields_ids = array();// store all the ids of incoming fields
			foreach($new_copy_fields as $field) {
				$new_copy_fields_ids[] = $field['id'];
			}
			$new_copy_fields_new_ids = array();// store lookup for new ids on incoming fields (where key is old id and value is new id)

			$f = 0;
			foreach($new_copy_fields as $field) {

				$new_copy_fields_new_ids[ $field['id'] ] = $target_form_last_field_id;// add new id to lookup
				$new_copy_fields[$f]['id'] = $target_form_last_field_id;// assign new id

				if (isset($field['inputs']) && !empty($field['inputs'])) {
					$i = 0;
					foreach($field['inputs'] as $input) {
						$inputID = substr($input['id'], strpos($input['id'], ".") + 1);
						$new_copy_fields[$f]['inputs'][$i]['id'] = "".$target_form_last_field_id.".".$inputID;
						$i++;
					}
				}

				$target_form_last_field_id++;
				$f++;
			}

			// update conditional logic on incoming fields
			$f = 0;
			foreach($new_copy_fields as $field) {
				if (isset($field['conditionalLogic']) && !empty($field['conditionalLogic']) && isset($field['conditionalLogic']['rules']) && !empty($field['conditionalLogic']['rules'])) {
					$r = 0;
					$new_rules = array();
					foreach($field['conditionalLogic']['rules'] as $conditionalLogicRule) {
						$ref_id = (int) $conditionalLogicRule['fieldId'];
						if (in_array($ref_id, $new_copy_fields_ids)) {
							// if a rule relates to one of the incoming fields, update the id
							$new_id = $new_copy_fields_new_ids[ $conditionalLogicRule['fieldId'] ];
							$conditionalLogicRule['fieldId'] = $new_id;
							$new_rules[] = $conditionalLogicRule;
						}
						$r++;
					}

					if (!empty($new_rules)) {
						$new_copy_fields[$f]['conditionalLogic']['rules'] = $new_rules;
					}
					else {
						$new_copy_fields[$f]['conditionalLogic'] = '';
					}

				}

				$f++;
			}

			// update calculations incoming fields
			$f = 0;
			foreach($new_copy_fields as $field) {
				if (isset($field['enableCalculation']) && !empty($field['enableCalculation']) && isset($field['calculationFormula']) && !empty($field['calculationFormula'])) {
					$matches = array();
					$match_ids = preg_match_all('/:(.*?)}/s', $field['calculationFormula'], $matches);
					$calculation_field_ids = $matches[1];
					$new_calculation_field_ids = array();
					foreach($calculation_field_ids as $ref_id) {
						if (in_array($ref_id, $new_copy_fields_ids)) {
							// if a rule relates to one of the incoming fields, update the id
							$new_id = $new_copy_fields_new_ids[$ref_id];
							$new_calculation_field_ids[] = $new_id;
						}
					}

					if (!empty($new_calculation_field_ids) && count($new_calculation_field_ids) == count($calculation_field_ids)) {
						$m = 0;
						$new_calculation_formula = $field['calculationFormula'];
						foreach($matches[0] as $match) {
							$replacement = ':'.$new_calculation_field_ids[$m].'}';
							$new_calculation_formula = str_replace($match, $replacement, $new_calculation_formula);
							$m++;
						}
						$field['calculationFormula'] = $new_calculation_formula;
					}
					else {
						// clear calculation
						$field['calculationFormula'] = '';
						$field['enableCalculation'] = false;
					}

				}

				$f++;
			}

			// update productField id on product option fields
			$f = 0;
			foreach($new_copy_fields as $field) {
				if (isset($field['productField']) && !empty($field['productField'])) {
					$r = 0;
					$new_product_field = '';
					if (in_array($field['productField'], $new_copy_fields_ids)) {
						// if productField relates to one of the incoming fields, update the id
						$new_id = $new_copy_fields_new_ids[ $field['productField'] ];
						$new_product_field = $new_id;
						$new_copy_fields[$f]['productField'] = $new_product_field;
					}

					if (empty($new_product_field)) {
						unset($new_copy_fields[$f]['productField']);
					}
				}

				$f++;
			}

			// add fields to target form
			foreach($new_copy_fields as $field) {
				$target_form['fields'][] = $field;
			}

			$target_form['nextFieldId'] = $target_form_last_field_id;
			$update_result = GFAPI::update_form($target_form);

			if (is_wp_error($update_result)) {
				$update_errors++;
				$update_results[$target_form_id] = array(
                    'updated' => false,
                    'title' => $target_form['title']
                );
            }
            else {
	            $update_results[$target_form_id] = array(
		            'updated' => true,
		            'title' => $target_form['title']
	            );
            }

        }

        if ($update_errors > 0) {
		    // echo 'failed';
        }
        echo json_encode(array(
            'results' => $update_results,
            'errors' => $update_errors
        ));
		exit;

	}

	/**
	 * Custom toolbar link item within form admin
	 */

	public function gf_bulk_actions_toolbar_item( $menu_items, $form_id ) {

		$menu_items['gf_bulk_actions_link'] = array(
			'label' => 'Bulk Actions',
			'title' => 'Run bulk actions for this form',
			'url' => self_admin_url('admin.php?page='.$this->_slug.'&gform_id='.$form_id ),
			'menu_class' => 'gf_bulk_actions_pro',
			'link_class' => rgget('page') == 'gf_bulk_actions_pro' ? 'gf_toolbar_active gf_bulk_actions_link_active' : 'gf_bulk_actions_link',
	        'capabilities' => array('gravityforms_edit_forms'),
	        'priority' => 999
	    );

        return $menu_items;
	}

	/**
	 * Custom quick link on Forms page
	 */

	public function gf_bulk_actions_quick_link($actions, $form_id) {
		$actions['gf_bulk_actions_link'] = '<a href="' . self_admin_url('admin.php?page='.$this->_slug.'&gform_id='.$form_id).'">' . __( 'Bulk Actions', 'gf_bulk_actions_pro' ) . '</a>';
        return $actions;
	}



	/**
	 * Add custom messages after plugin row based on license status
	 */

	public function gf_plugin_row($plugin_file='', $plugin_data=array(), $status='') {
		$license_key = trim($this->get_plugin_setting('gf_bulk_actions_pro_license_key'));
		$license_status = get_option('gf_bulk_actions_pro_license_status', '');
		$row = array();
		if (empty($license_key) || empty($license_status)) {
			$row = array(
				'<tr class="plugin-update-tr">',
					'<td colspan="3" class="plugin-update gf_bulk_actions-pro-plugin-update">',
						'<div class="update-message">',
							'<a href="' . admin_url('admin.php?page=gf_settings&subview=' . $this->_slug) . '">Activate</a> your license to receive plugin updates and support. Need a license key? <a href="' . $this->_website . '" target="_blank">Purchase one now</a>.',
						'</div>',
                        '<style type="text/css">',
                        '.plugin-update.gf_bulk_actions-pro-plugin-update .update-message:before {',
                            'content: "\f348";',
                            'margin-top: 0;',
                            'font-family: dashicons;',
                            'font-size: 20px;',
                            'position: relative;',
                            'top: 5px;',
                            'color: orange;',
                            'margin-right: 8px;',
                        '}',
                        '.plugin-update.gf_bulk_actions-pro-plugin-update {',
                            'background-color: #fff6e5;',
                        '}',
                        '.plugin-update.gf_bulk_actions-pro-plugin-update .update-message {',
                            'margin: 0 20px 6px 40px !important;',
                            'line-height: 28px;',
                        '}',
                        '</style>',
					'</td>',
				'</tr>'
			);
		}
		elseif(!empty($license_key) && $license_status != 'valid') {
			$row = array(
				'<tr class="plugin-update-tr">',
					'<td colspan="3" class="plugin-update gf_bulk_actions-pro-plugin-update">',
						'<div class="update-message">',
							'Your license is invalid or expired. <a href="'.admin_url('admin.php?page=gf_settings&subview='.$this->_slug).'">Enter valid license key</a> or <a href="'.$this->_website.'" target="_blank">purchase a new one</a>.',
							'<style type="text/css">',
								'.plugin-update.gf_bulk_actions-pro-plugin-update .update-message:before {',
                                    'content: "\f348";',
                                    'margin-top: 0;',
                                    'font-family: dashicons;',
                                    'font-size: 20px;',
                                    'position: relative;',
                                    'top: 5px;',
                                    'color: #d54e21;',
                                    'margin-right: 8px;',
								'}',
                                '.plugin-update.gf_bulk_actions-pro-plugin-update {',
                                    'background-color: #ffe5e5;',
                                '}',
								'.plugin-update.gf_bulk_actions-pro-plugin-update .update-message {',
                                    'margin: 0 20px 6px 40px !important;',
                                    'line-height: 28px;',
								'}',
							'</style>',
						'</div>',
					'</td>',
				'</tr>'
			);
		}
		echo implode('', $row);
	}



	/**
	 * Determine if the license key is valid so the appropriate icon can be displayed next to the field.
	 *
	 * @param string $value The current value of the license_key field.
	 * @param array $field The field properties.
	 *
	 * @return bool|null
	 */
	public function license_feedback( $value, $field ) {
		if ( empty( $value ) ) {
			return null;
		}

		// Send the remote request to check the license is valid
		$license_data = $this->perform_edd_license_request( 'check_license', $value );

		$valid = null;
		if ( empty( $license_data ) || !is_object($license_data) || !property_exists($license_data, 'license') || $license_data->license == 'invalid' ) {
			$valid = false;
		}
		elseif ( $license_data->license == 'valid' ) {
			$valid = true;
		}

		if (!empty($license_data) && is_object($license_data) && property_exists($license_data, 'license')) {
			update_option('gf_bulk_actions_pro_license_status', $license_data->license);
		}

		return $valid;
	}


	/**
	 * Handle license key activation or deactivation.
	 *
	 * @param array $field The field properties.
	 * @param string $field_setting The submitted value of the license_key field.
	 */
	public function license_validation( $field, $field_setting ) {
		$old_license = $this->get_plugin_setting( 'gf_bulk_actions_pro_license_key' );

		if ( $old_license && $field_setting != $old_license ) {
			// Send the remote request to deactivate the old license
			$response = $this->perform_edd_license_request( 'deactivate_license', $old_license );
			if ( !empty($response) && is_object($response) && property_exists($response, 'license') && $response->license == 'deactivated' ) {
				delete_option('gf_bulk_actions_pro_license_status');
			}
		}

		if ( ! empty( $field_setting ) ) {
			// Send the remote request to activate the new license
			$response = $this->perform_edd_license_request( 'activate_license', $field_setting );
			if ( !empty($response) && is_object($response) && property_exists($response, 'license') ) {
				update_option('gf_bulk_actions_pro_license_status', $response->license);
			}
		}
	}


	/**
	 * Send a request to the EDD store url.
	 *
	 * @param string $edd_action The action to perform (check_license, activate_license or deactivate_license).
	 * @param string $license The license key.
	 *
	 * @return object
	 */
	public function perform_edd_license_request( $edd_action, $license ) {
		// Prepare the request arguments
		$args = array(
			'timeout' => GF_BULK_ACTIONS_TIMEOUT,
			'sslverify' => GF_BULK_ACTIONS_SSL_VERIFY,
			'body' => array(
				'edd_action' => $edd_action,
				'license' => trim($license),
				'item_name' => urlencode(GF_BULK_ACTIONS_NAME),
				'url' => home_url(),
			)
		);

		// Send the remote request
		$response = wp_remote_post(GF_BULK_ACTIONS_HOME, $args);

		return json_decode( wp_remote_retrieve_body( $response ) );
	}


}