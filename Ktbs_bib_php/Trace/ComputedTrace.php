<?php

require_once dirname(dirname( __FILE__ ))."/Global.php" ;
require_once $path_RestfulHelper ;
         /* This class is used to Create a Computed Trace.*/
 
class ComputedTrace{

	public $base_uri = null ;
	public $type = null;
	public $model_uri = null ;
	public $name = null ;
	public $hasOrigin = null ;
	public $uri = null ;
	public $hasMethod = null ; 
	public $hasParameter = array() ; 
	public $hasSource = array() ;
	public $sourceOf = array() ;

	//done

	function __construct(){

		$a = func_get_args();
		$i = func_num_args();

		if(method_exists($this, $f = '__construct'.$i)){

			call_user_func_array(array($this,$f), $a);

		}
	}

	function __construct1($uri){

		if($uri[strlen($uri) - 1] == '/'){

			$this->uri = $uri;
		}
		else{

			$uri = $uri.'/';
			$this->uri = $uri;
		}

			if($this->exist()){

			$tab = explode('/', $this->uri);
			$n = sizeof($tab);
			$str = $tab[$n-2].'/';
			$base = str_replace($str, '', $uri);
			$this->name = $str;
			$this->base_uri = $base;

			$reponse = RestfulHelper::getInfo($this->uri);
			
	        if ($reponse){

		        $obj= json_decode($reponse,true) ;
		        $ModelURL = $obj['hasModel'];
		        $this->type = $obj["@type"];
		        $ModelURL = str_replace('../', '', $ModelURL);
		        $this->model_uri = $this->base_uri.$ModelURL;
		        $this->hasOrigin = $obj['origin'];

		        if(array_key_exists("isSourceOf", $obj)){

		        	$source = $obj['isSourceOf'];

			        for ($i=0; $i <sizeof($source) ; $i++) { 

			        	$source[$i] = str_replace('../', $this->base_uri, $source[$i]);
		        	}

		        	$this->sourceOf = $source;
		        }

		        if(array_key_exists('hasSource', $obj)){

		        	$s = $obj['hasSource'];
		        	for ($i=0; $i <sizeof($s) ; $i++) { 

			        	$s[$i] = str_replace('../', $this->base_uri, $s[$i]);
			        	$this->hasSource = $s;
		        	}
		        }
		        $this->hasMethod = $obj['hasMethod'];
		        $this->hasParameter = $obj['parameter'];


			}

		}	
	}

	function __construct2($base_uri,$trace_Name,$model_uri=null){

		//$this->hasOrigin = $this->getTime();
		//$this->hasParameter[] = '"origin='.$this->hasOrigin.'"';

		if($base_uri[strlen($base_uri)-1] == "/"){

			$this->base_uri = $base_uri;
		}
		else {
			$this->base_uri = $base_uri."/";
		}
		if($trace_Name[strlen($trace_Name)-1] == "/"){

			$this->name = $trace_Name;
		}
		else {

			$this->name = $trace_Name."/";
		}
		if($model_uri){

			if ($model_uri[strlen($model_uri)-1]=="/"){

			$model_uri[strlen($model_uri)-1] = "";
			$this->model_uri = $model_uri;

			}
			else{

				$this->model_uri = $model_uri;
			}

		}
		


		$this->uri = $this->base_uri.$this->name ;	
	}

	function __construct3($base_uri,$trace_Name,$model_uri){

		//$this->hasOrigin = $this->getTime();
		//$this->hasParameter[] = '"origin='.$this->hasOrigin.'"';

		if($base_uri[strlen($base_uri)-1] == "/"){

			$this->base_uri = $base_uri;
		}
		else {
			$this->base_uri = $base_uri."/";
		}
		if($trace_Name[strlen($trace_Name)-1] == "/"){

			$this->name = $trace_Name;
		}
		else {

			$this->name = $trace_Name."/";
		}
		if($model_uri){

			if ($model_uri[strlen($model_uri)-1]=="/"){

			$model_uri[strlen($model_uri)-1] = "";
			$this->model_uri = $model_uri;

			}
			else{

				$this->model_uri = $model_uri;
			}

		}
		
		
		$this->uri = $this->base_uri.$this->name ;	
	}

	function getModelSource(){

		foreach ($this->hasSource as $key => $value) {
			if($value->getType() == "StoredTrace"){
				return $value->getModel();
				break;
			}
			else{
				$m = new ComputedTrace($value->getUri());
				$m->getModelSource();
			}
			

		}
	}

	function getModel(){

		return $this->model_uri;
	}

	function getBaseUri(){

		return $this->base_uri;
	}

	function getUri(){

		return $this->uri;
	}

	function getType(){

		return $this->type;
	}

	function getName(){

		return $this->name;
	}

	function getOrigin(){

		return $this->hasOrigin;
	}

	function getObsels(){

		$reponse = RestfulHelper::getInfo($this->uri."@obsels");
        if ($reponse){
        $ob =json_decode($reponse,true);
        $obsels = $ob['obsels'];
        return $obsels;
    	}
	}

	function getIsSourceOf(){

		return $this->sourceOf;
	}

	function getSource(){

		if($this->exist()){

			return $this->hasSource;
		}
	}

	function getParameter(){

		return $this->hasParameter;
	}

	function getMethod(){

		return $this->hasMethod;
	}
	//done
	function setFilterParameter($after=null,$before=null,$otypes=null){

		$a = null;
		$b = null; 
		$ty = null;

		if ($this->hasMethod == 'filter') {

			if(is_int($after)){

				$a='"after='."$after".'"';
				$this->hasParameter[] = $a;
			}
			if(is_string($after)){

				$v = strtotime($after);
				if($v){

					$v = $v*1000;
					$a='"after='."$v".'"';
					$this->hasParameter[] = $a;
				}
				
			}

			if(is_int($before)){

				$b='"before='."$before".'"';
				$this->hasParameter[] = $b;
			}
			if(is_string($before)){
				$v = strtotime($before);
				if($v){

					$v = $v*1000;
					$b='"before='."$v".'"';
					$this->hasParameter[] = $b;
				}
			}
			$trace = $this->hasSource[0];
			// $model = "";
			// if($trace->getType() == "StoredTrace"){
				// $model = $trace->getModel();
			// }
			// if($trace->getType == "ComputedTrace"){


			// 	$m = new ComputedTrace($trace->getUri());
			// 	$model = $model->getModelSource();

			// }
			$model = $trace->getModel();
			
			if(is_array($otypes)){
				$ty = '"otypes=';
				foreach ($otypes as $key => $value) {
					
					$ty = $ty.$model."#".$value.' ';
				}
				$ty[strlen($ty)-1] = '"';
				$this->hasParameter[] = $ty;
			}
			if ($this->model_uri == null){
				$this->model_uri = $trace->getModel();
			}

			$this->hasParameter[] = '"model='.$this->model_uri.'"';
			
			
			

		}
	}
	//TODO
	function config($method,$sources){

		if (is_array($sources)){

			$n = sizeof($sources);
			switch ($method) {

				case 'sparql':

						if ($n == 1) {

							$this->hasMethod = $method;
							$this->hasSource = $sources;
							$tr = $this->hasSource[0];
							if($this->model_uri == null){

								$this->model_uri = $tr->getModel();	
							}
							
						}
					
					break;

				case 'filter':

						if ($n == 1) {

							$this->hasMethod = $method;
							$this->hasSource = $sources;
						}

					break;

				case 'fusion':

						if ($n > 1) {

							$this->hasMethod = $method;
							$this->hasSource = $sources;
						}

					break;
			}

		}
	}
	//done
	function filter(){

		if ($this->hasMethod == 'filter' && sizeof($this->hasSource)==1 && is_array($this->hasSource)){
			$tr = $this->hasSource[0];
			$prefixes[] = "@prefix : <http://liris.cnrs.fr/silex/2009/ktbs#> .";	

			$statements[] = "<> :contains <".$this->name."> .";
			$statements[] = "<".$this->name."> a :ComputedTrace ;";	
			$statements[] = ":hasMethod :filter ;";
			$statements[] = ":hasSource ".$this->listSources()." ;";
			$statements[] = ':hasParameter '. str_replace(',,', ',',implode(',', $this->hasParameter)).' .' ;
			$this->script =  implode("\n", $prefixes)."\n".implode("\n", $statements);

			//echo htmlentities($this->script);
			//var_export($this->script);
			$this->result = RestfulHelper::post($this->base_uri, $this->script);
			$tr = $this->hasSource[0];


			if($this->model_uri != $tr->getModel()){

				$model = new TraceModel($this->model_uri);
				$Gene = new GenerateModel($this->uri,$model->getName());
				$Gene->PutModel();
			}

						

		}
	}
	//done
	function SameModelSameBase(){

		$v = true;
		if(sizeof($this->hasSource)>1 && $this->hasMethod == 'fusion'){

			$T = $this->hasSource[0];
			$m = $T->getModel();
			$b = $T->getBaseURI();

			foreach ($this->hasSource as $key => $value) {

				if ($value->getModel() != $m || $value->getBaseURI() != $b){

					$v = false;
					break;
				}
			}
		}
		return $v;
	}
	//done
	function fusion(){

		if($this->SameModelSameBase() && $this->hasMethod == 'fusion'){
			$tr = $this->hasSource[0];
			if($this->model_uri == null){

				$this->model_uri = $tr->getModel();
			}
			$this->hasParameter[] = '"model='.$this->model_uri.'"';
			$prefixes[] = "@prefix : <http://liris.cnrs.fr/silex/2009/ktbs#> .";	

			$statements[] = "<> :contains <".$this->name."> .";
			$statements[] = "<".$this->name."> a :ComputedTrace ;";	
			$statements[] = ":hasMethod :fusion ;";
			$statements[] = ":hasSource ".$this->listSources()." ;";
			$statements[] = ':hasParameter '.implode(',', $this->hasParameter).' .' ;
			$this->script =  implode("\n", $prefixes)."\n".implode("\n", $statements);

			$this->result = RestfulHelper::post($this->base_uri, $this->script);

			if($this->model_uri != $tr->getModel()){

				$model = new TraceModel($this->model_uri);
				$Gene = new GenerateModel($this->uri,$model->getName());
				$Gene->PutModel();
			}

		}
	}
	//done
	function listSources(){

		if($this->hasSource){
			$list = array();
			foreach ($this->hasSource as $tr) {
				$a = $tr->getName();
				//echo $a;
				$list[] = '<'.$a.'>';
			}
			//echo implode(", ",$list);
			return implode(", ",$list);


		}
	}
	//inutile
	function exec(){
		if($this->hasMethod && $this->hasParameter && $this->hasSource){


			$prefixes[] = "@prefix : <http://liris.cnrs.fr/silex/2009/ktbs#> .";				
		
			$statements[] = "<> :contains <".$this->name."> .";
			$statements[] = "<".$this->name."> a :ComputedTrace ;";	
			$statements[] = ":hasMethod :".$this->hasMethod." ;";
			$statements[] = ":hasSource ".$this->hasSource." ;";
			$statements[] = ':hasParameter '.$this->hasParameter.'.';
			
			$this->script = implode("\n", $prefixes)."\n".implode("\n", $statements);
			$this->result = RestfulHelper::post($this->base_uri, $this->script);



		}
	}
	//done
	function exist(){

	    $this->exist = RestfulHelper::get($this->uri);
        return $this->exist;
	}
	//done
	function Delete(){

	 $reponse = RestfulHelper::getInfo($this->uri);
        RestfulHelper::Delete($this->uri,$reponse);
	}
	//done
	function getTime(){
		$datetime = new DateTime();
		$m = explode(' ',microtime());		
		$microSeconds = $m[0];
		$milliSeconds = (int)round($microSeconds*1000,3);
		$seconds = $m[1];
		$datetime->setTimezone(new DateTimeZone('UTC'));
		$now = $datetime->format('Y-m-d')."T".$datetime->format('H:i:s').".".str_pad($milliSeconds,3,"0",STR_PAD_LEFT)."Z";
		return $now;
	}

	function sparql(){

		if ($this->hasMethod == 'sparql'){

			$tr = $this->hasSource[0];
			if($this->model_uri == null){

				$this->model_uri = $tr->getModel();
			}
			$this->hasParameter[] = '"model='.$this->model_uri.'"';
			$prefixes[] = "@prefix : <http://liris.cnrs.fr/silex/2009/ktbs#> .";	

			$statements[] = "<> :contains <".$this->name."> .";
			$statements[] = "<".$this->name."> a :ComputedTrace ;";	
			$statements[] = ":hasMethod :sparql ;";
			$statements[] = ":hasSource ".$this->listSources()." ;";
			$statements[] = ':hasParameter '.implode(',', $this->hasParameter).' .' ;
			$this->script =  implode("\n", $prefixes)."\n".implode("\n", $statements);

			$this->result = RestfulHelper::post($this->base_uri, $this->script);

			if($this->model_uri != $tr->getModel()){

				$model = new TraceModel($this->model_uri);
				$Gene = new GenerateModel($this->uri,$model->getName());
				$Gene->PutModel();
			}
		}
	}

	function setSparqlParameter($TypeObsel,$AttributeCond,$condition){
		$tr = $this->hasSource[0];
		$model = $tr->getModel();
		$sparql = "sparql=".SparqlGenerate::generatePrefix()."CONSTRUCT{".SparqlGenerate::generateALLSELECT().
                          "} WHERE {".SparqlGenerate::generateConditionFiltreALL($model, $AttributeCond, $TypeObsel ,$condition )."}";
        $Parametre = '"'."inherit=true".'",'.'"""'.$sparql.'"""';
        $this->hasParameter[] = $Parametre;
        
	}
		function setSparqlParameter2($TypeObsel,$AttributeCond,$condition){
		$tr = $this->hasSource[0];
		$model = $tr->getModel();
		$sparql = "sparql=".SparqlGenerate::generatePrefix()."CONSTRUCT{".SparqlGenerate::generateSELECT($model,$AttributeCond).
                          "} WHERE {".SparqlGenerate::generateConditionFiltre($model, $AttributeCond, $TypeObsel ,$condition )."}";
        $Parametre = '"""'.$sparql.'"""';
        $this->hasParameter[] = $Parametre;
        
	}
}

?>
