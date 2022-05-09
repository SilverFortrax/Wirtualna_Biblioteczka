<?php
require_once $conf->root_path.'/lib/smarty/Smarty.class.php';
require_once $conf->root_path.'/lib/Messages.class.php';
require_once $conf->root_path.'/app/calc/CalcForm.class.php';
require_once $conf->root_path.'/app/calc/CalcResult.class.php';


class CalcCtrl {

	private $msgs;  
	private $form;  
	private $result; 
	 
	
	public function __construct(){
		$this->msgs = new Messages();
		$this->form = new CalcForm();
		$this->result = new CalcResult();
	}
	public function getParams(){
		$this->form->x = isset($_REQUEST ['x']) ? $_REQUEST ['x'] : null;
		$this->form->y = isset($_REQUEST ['y']) ? $_REQUEST ['y'] : null;
		$this->form->z = isset($_REQUEST ['z']) ? $_REQUEST ['z'] : null;
	}
	
	public function validate() {
		if (! (isset ( $this->form->x ) && isset ( $this->form->y ) && isset ( $this->form->z ))) {
			return false;
		}
		
		if ($this->form->x == "") {
			$this->msgs->addError('Nie podano wartości pożyczki.');
		}
		if ($this->form->y == "") {
			$this->msgs->addError('Nie podano ilości rat.');
		}
		if ($this->form->z == "") {
			$this->msgs->addError('Nie podano oprocentowania.');
		}
		
		if (! $this->msgs->isError()) {
	
			if (! is_numeric ( $this->form->x )) {
				$this->msgs->addError('Wartość pożyczki musi zostać zapisana jako liczba.');
			}
			
			if (! is_numeric ( $this->form->y )) {
				$this->msgs->addError('Ilość rat musi być zapisana jako liczba całkowita.');
			}

			if (! is_numeric ( $this->form->z )) {
				$this->msgs->addError('Oprocentowanie musi zostać zapisane jako liczba.');
			}
		}
		
		return ! $this->msgs->isError();
	}

	public function process(){

		$this->getParams();
		
		if ($this->validate()) {

			$this->form->x = floatval($this->form->x);
			$this->form->y = intval($this->form->y);
			$this->form->z = doubleval($this->form->z);
			$this->msgs->addInfo('Parametry poprawne.');

			$this->result->result = ($this->form->x + ($this->form->x * ($this->form->z / 100)))/$this->form->y;
					$this->result->result = round($this->result->result);
				
			
			$this->msgs->addInfo('Wykonano obliczenia.');
		}	
		$this->generateView();
	}

	public function generateView(){
		global $conf;
		
		$smarty = new Smarty();
		$smarty->assign('conf',$conf);
		
		$smarty->assign('page_title','Projekt 06 Silver');
		$smarty->assign('page_description','Aplikacja z jednym "punktem wejścia". Model MVC, w którym jeden główny kontroler używa różnych obiektów kontrolerów w zależności od wybranej akcji - przekazanej parametrem.');
		$smarty->assign('page_header','Kontroler główny');
					
		$smarty->assign('msgs',$this->msgs);
		$smarty->assign('form',$this->form);
		$smarty->assign('res',$this->result);
		
		$smarty->display($conf->root_path.'/app/calc/CalcView.html');
}
}