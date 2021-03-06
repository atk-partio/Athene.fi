<?php
/**
* CCTM_wysiwyg
*
* Implements an WYSIWYG textarea input (a textarea with formatting controls).
*
*/
class CCTM_wysiwyg extends CCTMFormElement
{

	/** 
	* The $props array acts as a template which defines the properties for each instance of this type of field.
	* When added to a post_type, an instance of this data structure is stored in the array of custom_fields. 
	* Some properties are required of all fields (see below), some are automatically generated (see below), but
	* each type of custom field (i.e. each class that extends CCTMFormElement) can have whatever properties it needs
	* in order to work, e.g. a dropdown field uses an 'options' property to define a list of possible values.
	* 
	* 
	*
	* The following properties MUST be implemented:
	*	'name' 	=> Unique name for an instance of this type of field; corresponds to wp_postmeta.meta_key for each post
	*	'label'	=> 
	*	'description'	=> a description of this type of field.
	*
	* The following properties are set automatically:
	*
	* 	'type' 			=> the name of this class, minus the CCTM_ prefix.
	* 	'sort_param' 	=> populated via the drag-and-drop behavior on "Manage Custom Fields" page.
	*/
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra'	=> 'cols="80" rows="10"',
		'default_value' => '',
		// 'type'	=> '', // auto-populated: the name of the class, minus the CCTM_ prefix.
		// 'sort_param' => '', // handled automatically
	);

	//------------------------------------------------------------------------------
	/**
	* This function provides a name for this type of field. This should return plain
	* text (no HTML). The returned value should be localized using the __() function.
	* @return	string
	*/
	public function get_name() {
		return __('WYSIWYG',CCTM_TXTDOMAIN);	
	}
	
	//------------------------------------------------------------------------------
	/**
	* This function gives a description of this type of field so users will know 
	* whether or not they want to add this type of field to their custom content
	* type. The returned value should be localized using the __() function.
	* @return	string text description
	*/
	public function get_description() {
		return __('What-you-see-is-what-you-get (WYSIWYG) fields implement a <textarea> element with formatting controls. 
			"Extra" parameters, e.g. "cols" can be specified in the definition, however a minimum size is required to make room for the formatting controls.',CCTM_TXTDOMAIN);
	}
	
	//------------------------------------------------------------------------------
	/**
	* This function should return the URL where users can read more information about
	* the type of field that they want to add to their post_type. The string may
	* be localized using __() if necessary (e.g. for language-specific pages)
	* @return	string 	e.g. http://www.yoursite.com/some/page.html
	*/
	public function get_url() {
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/WYSIWYG';
	}
	

	//------------------------------------------------------------------------------
	/**
	 *
	 * @param string $current_value	current value for this field.
	 * @return string	
	 <label for="[+name+]" class="cctm_label cctm_textarea_label" id="cctm_label_[+name+]">[+label+]</label>
			<textarea name="[+name+]" class="cctm_textarea" id="[+name+]" [+extra+]>[+value+]</textarea>
	 */
	public function get_edit_field_instance($current_value) {
		
		// print "Here------->".$current_value; exit;
		// See Issue http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=138
		// See http://keighl.com/2010/04/switching-visualhtml-modes-with-tinymce/
		ob_start();
		wp_tiny_mce(false, // true makes the editor "teeny"
		    array(
		    "editor_selector" => $this->get_field_name(),
		    "height" => 150,
		    )
		  );
		$output = ob_get_contents();
		ob_end_clean();

		$output .= sprintf('
			<script type="text/javascript">
				jQuery( document ).ready( function() {
					jQuery( "%s" ).addClass( "mceEditor" );
					if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
						tinyMCE.execCommand( "mceAddControl", false, "%s" );
					}
				});
			</script>		
			<p align="right">
			  <a class="button" onclick="javascript:show_rtf_view(\'%s\');">Visual</a>
			  <a class="button" onclick="javascript:show_html_view(\'%s\');">HTML</a>
			</p>
			%s
			<textarea name="%s" class="%s " id="%s" %s>%s</textarea>

			<br />
			'
			, $this->get_field_name()
			, $this->get_field_name()

			, $this->get_field_id()
			, $this->get_field_id()
			, $this->wrap_label()
			, $this->get_field_name()
			// They all must have the same class name.
			// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=171
			, 'cctm_wysiwyg' // $this->get_field_class($this->name, 'wysiwyg') . ' ' . $this->class
			, $this->get_field_id()
			, $this->extra
			, $current_value
		);
		
		$output .= $this->wrap_description($this->props['description']);
		
		return $this->wrap_outer($output);
	}

/*
		ob_start();
		wp_tiny_mce(false, // true makes the editor "teeny"
		    array(
		    "editor_selector" => $this->get_field_name(),
		    "height" => 150
		    )
		  );
		$output = ob_get_contents();
		ob_end_clean();

		$output .= sprintf('
			%s
			<textarea name="%s" class="%s " id="%s" %s>%s</textarea>
			<script type="text/javascript">
				jQuery( document ).ready( function() {
					jQuery( "%s" ).addClass( "mceEditor" );
					if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
						tinyMCE.execCommand( "mceAddControl", false, "%s" );
					}
				});
			</script>
			'
			, $this->wrap_label()
			, $this->get_field_name()
			, $this->get_field_class($this->name, 'wysiwyg') . ' ' . $this->class
			, $this->get_field_id()
			, $this->extra
			, $current_value
			, $this->get_field_name()
			, $this->get_field_name()
		);
		
		$output .= $this->wrap_description($this->props['description']);
		
		return $this->wrap_outer($output);
	}
*/



	//------------------------------------------------------------------------------
	/**
	 *
	 * @param mixed $def	field definition; see the $props array
	 */
	public function get_edit_field_definition($def) {
		
		// Label
		$out = '<div class="'.self::wrapper_css_class .'" id="label_wrapper">
			 		<label for="label" class="'.self::label_css_class.'">'
			 			.__('Label', CCTM_TXTDOMAIN).'</label>
			 		<input type="text" name="label" class="'.self::css_class_prefix.'text" id="label" value="'.htmlspecialchars($def['label']) .'"/>
			 		' . $this->get_translation('label').'
			 	</div>';
		// Name
		$out .= '<div class="'.self::wrapper_css_class .'" id="name_wrapper">
				 <label for="name" class="cctm_label cctm_text_label" id="name_label">'
					. __('Name', CCTM_TXTDOMAIN) .
			 	'</label>
				 <input type="text" name="name" class="'.$this->get_field_class('name','text').'" id="name" value="'.htmlspecialchars($def['name']) .'"/>'
				 . $this->get_translation('name') .'
			 	</div>';
			 	
		// Default Value
		$out .= '<div class="'.self::wrapper_css_class .'" id="default_value_wrapper">
			 	<label for="default_value" class="cctm_label cctm_text_label" id="default_value_label">'
			 		.__('Default Value', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="default_value" class="'.$this->get_field_class('default_value','text').'" id="default_value" value="'. htmlspecialchars($def['default_value'])
			 		.'"/>
			 	' . $this->get_translation('default_value') .'
			 	</div>';

		// Extra
		$out .= '<div class="'.self::wrapper_css_class .'" id="extra_wrapper">
			 		<label for="extra" class="'.self::label_css_class.'">'
			 		.__('Extra', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="extra" class="'.$this->get_field_class('extra','text').'" id="extra" value="'
			 			.htmlspecialchars($def['extra']).'"/>
			 	' . $this->get_translation('extra').'
			 	</div>';

		// Class
		$out .= '<div class="'.self::wrapper_css_class .'" id="class_wrapper">
			 	<label for="class" class="'.self::label_css_class.'">'
			 		.__('Class', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="class" class="'.$this->get_field_class('class','text').'" id="class" value="'
			 			.htmlspecialchars($def['class']).'"/>
			 	' . $this->get_translation('class').'
			 	</div>';

		// Description	 
		$out .= '<div class="'.self::wrapper_css_class .'" id="description_wrapper">
			 	<label for="description" class="'.self::label_css_class.'">'
			 		.__('Description', CCTM_TXTDOMAIN) .'</label>
			 	<textarea name="description" class="'.$this->get_field_class('description','textarea').'" id="description" rows="5" cols="60">'.htmlspecialchars($def['description']).'</textarea>
			 	' . $this->get_translation('description').'
			 	</div>';
		return $out;
	}

	//------------------------------------------------------------------------------
	/**
	 * This function allows for custom handling of submitted post/page data just before
	 * it is saved to the database. Data validation and filtering should happen here,
	 * although it's difficult to enforce any validation errors.
	 *
	 * Note that the field name in the $_POST array is prefixed by CCTMFormElement::post_name_prefix,
	 * e.g. the value for you 'my_field' custom field is stored in $_POST['cctm_my_field']
	 * (where CCTMFormElement::post_name_prefix = 'cctm_').
	 *
	 * Output should be whatever string value you want to store in the wp_postmeta table
	 * for the post in question. This function will be called after the post/page has
	 * been submitted: this can be loosely thought of as the "on save" event
	 *
	 * @param mixed   	$posted_data  $_POST data
	 * @param string	$field_name: the unique name for this instance of the field
	 * @return	string	whatever value you want to store in the wp_postmeta table where meta_key = $field_name	
	 */
	public function save_post_filter($posted_data, $field_name) {
		$value = $posted_data[ CCTMFormElement::post_name_prefix . $field_name ];
		return wpautop( $value ); // Auto-paragraphs for any WYSIWYG
	}

}


/*EOF*/