<?php

namespace libs\forms;

class manager extends \libs\data\formService {

    private $valid = false;
    private $isPopulated = false;
    public $module;
    public $method = 'POST';

    const FORM_NOT_SUBMITTED = 0;
    const FORM_SUBMITTED = 1;
    const FORM_SUBMIT_FAILED = 2;
    const FORM_SUBMIT_PASSED = 4;
    const SHOW = 8;
    const INPUT_FILTER = 16;
    const OUTPUT_FILTER = 32;
    const PROCESS = 64;
    const PREFLIGHT = 128;
    const NEW_FORM = 'NEW';
    const FAILED = 'FAILED';
    const PASSED = 'PASSED';

    public function __construct($name, $definition = null) {

        $this->form_name = $name;

        if ($definition) {
            $this->definition = $definition;
        } else {
            if (get_called_class() != __CLASS__ && method_exists($this, 'getFormDefinition')) {
                $this->definition = $this->getFormDefinition();
            } else {
                throw new \Exception("Form cannot be created - no definition supplied or found within subclass");
            }
        }


        $this->inputFilterObject = new \libs\forms\filters;
        $this->inputFilterMethod = 'input';

        $this->outputFilterObject = $this->inputFilterObject;
        $this->outputFilterMethod = 'output';

        //include_once($_SERVER['DOCUMENT_ROOT']."/../app/libs/misc/recaptcha.functions.php");
        
        
    }

    public function isReady() {
        return $this->isSubmitted();
    }

    public function setInputFilter($cls, $method) {
        $this->inputFilterObject = $cls;
        $this->inputFilterMethod = $method;
    }

    public function setOutputFilter($cls, $method) {
        $this->outputFilterObject = $cls;
        $this->ouputFilterMethod = $method;
    }

    private function filterIn($type, $data) {
        return $this->inputFilterObject->{$this->inputFilterMethod}($type, $data);
    }

    private function filterOut($type, $data) {

        return $this->outputFilterObject->{$this->ouputFilterMethod}($type, $data);
    }

    public function Execute() {

        //$ci = $this->CheckIn(); //builds or modifies form data in session

        if ($this->isSubmitted()) {

            \Plugins\Plugins::Load()->DoPlugins('onFormSubmitted', $this);
            if ($this->CheckIn()) {
                \Plugins\Plugins::Load()->DoPlugins('onFormSubmitPass', $this);

                $this->valid = true;

                unset($_SESSION['form_data'][$this->form_name]);
            } else {
                \Plugins\Plugins::Load()->DoPlugins('onFormSubmitFail', $this);

                $this->valid = false;
            }
        } else {

            \Plugins\Plugins::Load()->DoPlugins('onFormRequested', $this);
        }
    }

    public function isSubmitted() {
        if ($_POST['_form_name'] == $this->form_name) {
            return true;
        }

        return false;
    }

    public function isValid() {
        return $this->valid;
    }

    public function isPopulated($p = false) {
        if ($p) {
            $this->isPopulated = true;
        }

        return $this->isPopulated;
    }

    public function reset() {

        unset($_POST['_form_name']);
        $_SESSION['form_data'][$this->form_name] = array();

        for ($i = 0; $i < count($this->definition); $i++) {
            unset($_SESSION['form_data'][$this->form_name][$i]['value']);
        }

        $this->Execute();
    }

    public function CheckIn() {
        
        $errors = 0;
        for ($i = 0; $i < count($this->definition); $i++) {

            $valid = true;

            //$this->definition[$i] = $this->preflight($this->definition[$i]);

            $_SESSION['form_data'][$this->form_name][$i] = $this->definition[$i];
            $_SESSION['form_data'][$this->form_name][$i]['errors'] = array();

            if ($this->isPopulated()) {
                $value = $_SESSION['form_data'][$this->form_name][$i]['value'];
            } else {
                $value = $this->filterIn($this->definition[$i]['input_type'], $_POST[$this->definition[$i]['name']]);
                //$value = $_POST[$this->definition[$i]['name']];
            }

            if ($this->definition[$i]['validate']) {

                list($v, $opts) = explode(":", $this->definition[$i]['validate']);
                $a = validation::$v($this->definition[$i]['name'], $opts);
                
                if ($this->definition[$i]['required'] && !$a) {
                    $valid = false;
                }

                switch ($this->definition[$i]['input_type']) {
                    case 'password':
                    case 'text':
                    case 'email':
                    case 'number':
                    case 'textarea':
                    case 'select':
                    case 'radio':
                        if (trim($_POST[$this->definition[$i]['name']]) != '' && !$a) {
                            $valid = false;
                        }
                        break;
                    case 'checkbox':
                        if (count($_POST[$this->definition[$i]['name']]) == 0 && !$a) {
                            $valid = false;
                        }
                        break;
                    default:
                        $valid = true;
                }



                if (!$valid) {
                    $_SESSION['form_data'][$this->form_name][$i]['errors'][] = "Submitted value does not match required type, " . $v;
                }
            }

            if ($this->definition[$i]['required'] && $value == '') {
                $_SESSION['form_data'][$this->form_name][$i]['errors'][] = "Required Field";
                $value = false;
            }

            if ($value === false || $valid === false) {
                $errors++;
            }

            $_SESSION['form_data'][$this->form_name][$i]['value'] = $value;
            $_SESSION['form_data'][$this->form_name][$i]['valid'] = $valid;
            $_SESSION['form_data'][$this->form_name][$i]['store_value'] = $value; //$this->filterIn($this->definition[$i]['input_type'], $_POST[$this->definition[$i]['name']]);

            $this->data[$this->definition[$i]['name']] = $_SESSION['form_data'][$this->form_name][$i]['store_value'];
        }

        $this->definition = &$_SESSION['form_data'][$this->form_name];
        return $errors == 0 ? true : false;
    }

    public function getData() {

        if ($this->isValid() && $this->isSubmitted()) {
            return $this->data;
        }

        return $_SESSION['form_data'][$this->form_name];
    }

    public function populate($data) {

        foreach ($this->definition as $i => $val) {

            $_SESSION['form_data'][$this->form_name][$i] = $this->definition[$i];
            $_SESSION['form_data'][$this->form_name][$i]['errors'] = array();

            if (is_array($data[$this->definition[$i]['name']])) {
                foreach ($data[$this->definition[$i]['name']] as $value) {
                    foreach ($this->definition[$i]['option_data'] as $k => $option) {
                        if ($option['post'] == $value['post']) {
                            $_SESSION['form_data'][$this->form_name][$i]['option_data'][$k]['selected'] = 'selected';
                        }
                    }
                }
            } else {
                $_SESSION['form_data'][$this->form_name][$i]['value'] = $this->filterOut($this->definition[$i]['input_type'], $data[$this->definition[$i]['name']], self::OUTPUT_FILTER);
            }
        }

        $this->isPopulated(1);
    }

}
