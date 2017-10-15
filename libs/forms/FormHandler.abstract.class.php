<?php

namespace libs\forms;

abstract class FormHandler implements iFormHandler {

	protected $form;
	protected $form_name;
	public $content;
	
	private $html_wrappers;

	public function __construct() {
		
		$this->form = $this->getFormDefinition();

		$this->html_wrappers['form']['start'] = "<table>";
		$this->html_wrappers['form']['end'] = "</table>";
		
		$this->html_wrappers['row']['start'] = "<tr>";
		$this->html_wrappers['row']['end'] = "</tr>";

		$this->html_wrappers['label']['start'] = "<td>";
		$this->html_wrappers['label']['end'] = "</td>";

		$this->html_wrappers['input']['start'] = "<td>";
		$this->html_wrappers['input']['end'] = "</td>";
		
		$this->html_wrappers['required']['start'] = "<span class='error'>";
		$this->html_wrappers['required']['end'] = "</span>";
		
		$this->submit_label = "Submit";
		
		$this->required_text = " *This is a required field";
		$this->valid_text = ' *This field has not been validated';
		
		$this->show_submit_label = true;

	}

	public function SetWrappers($item,$pos,$html) {
		$this->html_wrappers[$item][$pos] = $html;
	}
	
	public function setSubmitText($text) {
		$this->submit_label = $text;
	}
	
	public function setRequiredText($text) {
		$this->required_text = $text;
	}
	
	public function hideSubmitLabel() {
		$this->show_submit_label = false;
	}
	
	public function Execute() {
		if($this->isSubmitted()) {
			if($this->CheckIn()) {
				return $this->ProcessForm();
			} else {
				return $this->DrawForm();
			}
		} else {
			$_SESSION[$this->form_name] = array();
			return $this->DrawForm();
		}
	}
	
	public function isSubmitted() {
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			return true;
		}
		return false;
	}
	
	public function DrawForm($action=false) {
		
		$action = $action ? $action : $_SERVER['REQUEST_URI'];
		
		$content.="
		<form action='".$action."' method='post'>
			".$this->html_wrappers['form']['start']."
				";

		for($i=0;$i<count($this->form);$i++) {
		
			$valid = true;

			$value = $_SESSION[$this->form_name][$this->form[$i]['html_name']]['value'] == false ? '' : $_SESSION[$this->form_name][$this->form[$i]['html_name']]['value'];
			$req = '';

			$content.="
				".$this->html_wrappers['row']['start']."
					".$this->html_wrappers['label']['start']."<label for='".$this->form[$i]['html_name']."'>".$this->form[$i]['name']."</label>".$this->html_wrappers['label']['end']."
					".$this->html_wrappers['input']['start']."<input type='text' name='".$this->form[$i]['html_name']."' value='".$value."' />";

			if($this->form[$i]['required'] && $_SESSION[$this->form_name][$this->form[$i]['html_name']]['value'] === false) {
				$req = $this->required_text;
			}
			
			
			if($this->form[$i]['validate'] && $this->isSubmitted()) {
				$valid = call_user_func_array(array(__NAMESPACE__.'\FormHandler_Validation',$this->form[$i]['validate']),array($this->form[$i]['html_name']));
				
				if(($this->form[$i]['required'] && !$valid) || (!$this->form[$i]['required'] &&  trim($_POST[$this->form[$i]['html_name']]) != '' && !$a)) {
					$req.=$this->valid_text;
				}
			}
			

			if($this->form[$i]['required'] || !$valid) {
				$content.=$this->html_wrappers['required']['start'].$req.$this->html_wrappers['required']['end'];
			}

			$content.=$this->html_wrappers['input']['end']."
				".$this->html_wrappers['row']['end']."
			";

		}

		$submitLabel = '';
		
		if($this->show_submit_label) {
			$submitLabel = "<label for='".strtolower(str_replace(" ","_",$this->submit_label))."'>".$this->submit_label."</label>";
		}

		$content.="

				".$this->html_wrappers['row']['start']."
					".$this->html_wrappers['label']['start'].$submitLabel.$this->html_wrappers['label']['end']."
					".$this->html_wrappers['input']['start']."<input type='submit' name='".strtolower(str_replace(" ","_",$this->submit_label))."' value='".$this->submit_label."' />".$this->html_wrappers['input']['end']."
				".$this->html_wrappers['row']['end']."

			".$this->html_wrappers['form']['end']."

			<input type='hidden' name='submitted' value='1' />
			<input type='hidden' name='".sha1($this->form_name.$_SESSION['security_token'])."' value='1' />
		</form>
		";

		return $content;	
	}
	
	public function CheckIn() {
		$errors = 0;

		for($i=0;$i<count($this->form);$i++) {
			$valid = true;
			if($this->form[$i]['validate']) {
				$a = call_user_func_array(array(__NAMESPACE__.'\FormHandler_Validation',$this->form[$i]['validate']),array($this->form[$i]['html_name']));
				if($this->form[$i]['required'] && !$a) {
					$valid = false;
				}
				if(trim($_POST[$this->form[$i]['html_name']]) != '' && !$a) {
					$valid = false;
				}
			}
			
			if($this->form[$i]['required']) {
				$value = trim($_POST[$this->form[$i]['html_name']]) == '' ? false : $_POST[$this->form[$i]['html_name']];
			} else {
				$value = trim($_POST[$this->form[$i]['html_name']]);
			}

			if($value === false || $valid === false) {
				$errors++;
			}

			$_SESSION[$this->form_name][$this->form[$i]['html_name']]['value'] = $value;
		}

		return $errors == 0 ? true : false;	
	}
	
	public function HTMLise($form) {
		for($i=0;$i<count($form);$i++) {
			$form[$i]['html_name'] = strtolower(str_replace(" ","_",$form[$i]['name']));
		}
		
		return $form;	
	
	}
	
	public function ProcessForm() {
		
		$data = $_SESSION[$this->form_name];
		
		unset($_SESSION[$this->form_name]);
		
		$this->caller->processForm($data);
	}
	
}