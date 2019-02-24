<?php 
	namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use \App\Items;
	use \App\ItemsUsage;
	use \App\OrderDetails;
	use \App\Orders;
	use \App\Recipes;
	use \App\Tables;
	use Schema;

	class AdminOrdersController extends \crocodicstudio\crudbooster\controllers\CBController {



	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "orders";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Cliente","name"=>"cliente"];
			$this->col[] = ["label"=>"Mesa","name"=>"id_mesa","join"=>"tables,nombre"];
			$this->col[] = ["label"=>"Total","name"=>"total"];
			$this->col[] = ["label"=>"Estado","name"=>"estado","callback_php"=>'$row->estado==1?"activo":"inactivo";'];
			$this->col[] = ["label"=>"Saldo","name"=>"saldo"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Cliente','name'=>'cliente','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Mesa','name'=>'id_mesa','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'tables,id' ,'datatable_format'=>'id, \'-\',nombre','datatable_ajax'=>'false'];
			$this->form[] = ['label'=>'Estado','name'=>'estado','type'=>'radio','validation'=>'required|min:0','width'=>'col-sm-10','dataenum'=>'1|activo;0|inactivo', 'value'=>1];
			//$this->form[] = ['label'=>'saldo','name'=>'saldo','type'=>'money','validation'=>'','width'=>'col-sm-10', 'readonly'=>false];
			/*
				In order to have a master-detail form to manipulate orders in this case. we've got to write this lines
				columns = [];
				$columns[] = ['label'=>'Producto','name'=>'id_item','type'=>'datamodal','datamodal_table'=>'items','datamodal_columns'=>'nombre,precio_venta','datamodal_select_to'=>'precio_venta:precio','datamodal_where'=>'stock>0 or is_recipe=true','datamodal_size'=>'large', 'required'=>true];
				$columns[] = ['label'=>'Precio','name'=>'precio','type'=>'number','required'=>true, 'readonly'=>true];
				$columns[] = ['label'=>'Cantidad','name'=>'qty','type'=>'number','required'=>true];
				$columns[] = ['label'=>'Sub Total','name'=>'subtotal','type'=>'number','formula'=>"[qty] * [precio]","readonly"=>true,'required'=>true];
				what we did is to create an array with all the child information i.e. all their columns and their atributes 
				then in line 60 we define our child form. please read crudbooster documentation to get realize how this is made and what you could do with this.
			*/		
			$columns = [];
			$columns[] = ['label'=>'Producto','name'=>'id_item','type'=>'datamodal','datamodal_table'=>'items','datamodal_columns'=>'nombre,precio_venta','datamodal_select_to'=>'precio_venta:precio','datamodal_where'=>'stock>0 or is_recipe=true','datamodal_size'=>'large', 'required'=>true];
			$columns[] = ['label'=>'Precio','name'=>'precio','type'=>'number','required'=>true, 'readonly'=>true];
			$columns[] = ['label'=>'Cantidad','name'=>'qty','type'=>'number','required'=>true];
			$columns[] = ['label'=>'Sub Total','name'=>'subtotal','type'=>'number','formula'=>"[qty] * [precio]","readonly"=>true,'required'=>true];
			$this->form[] = ['label'=>'Detalles','name'=>'order_details','type'=>'child','columns'=>$columns,'validation'=>'required','width'=>'col-sm-9','table'=>'order_details','foreign_key'=>'id_order'];
			$this->form[] = ['label'=>'Total','name'=>'total','type'=>'money','validation'=>'required|min:0','width'=>'col-sm-10', 'readonly'=>false];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Cliente","name"=>"cliente","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Mesa","name"=>"id_mesa","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"mesa,id"];
			//$this->form[] = ["label"=>"Total","name"=>"total","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Estado","name"=>"estado","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Saldo","name"=>"saldo","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			# OLD END FORM

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
			$this->script_js = "
				setInterval(function(){
					var total = 0;
					$('#table-detalles tbody .subtotal').each(function(){
						var subt = parseInt($(this).text());
						total += subt;
					})
					$('#total').val(total);
				},500);
			";


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        
	        
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {
			//Your code here
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {        
			//Your code here
			/**
			 *
			 */
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
			//Your code here
			//erasing order details
			\App\OrderDetails::where('id_order', $id)->delete();

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }



	    //By the way, you can still create your own method in here... :) 
		public function postAddSave(){
			$this->cbLoader();
			//primero se comprueba si el user tiene permisos para realizar la acción, de lo contrario se le redirige.
			if (! CRUDBooster::isCreate() && $this->global_privilege == false) {
				CRUDBooster::insertLog(trans('crudbooster.log_try_add_save', [
					'name' => Request::input($this->title_field),
					'module' => CRUDBooster::getCurrentModule()->name,
				]));
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans("crudbooster.denied_access"));
			}

			//Se validan los datos una primera vez.
			$this->validation();
			//se asignan los datos a un array que es $this->arr en ese array está toda la información de nuestra petición. en cuanto a formularios
			$this->input_assignment();

			//se asigna fecha y hora de inserción
			if (Schema::hasColumn($this->table, 'created_at')) {
				$this->arr['created_at'] = date('Y-m-d H:i:s');
			}

			//si se ha sobreescrito el método HOOK_BEFORE ADD aquí se ejecutaría ese código.
			//Como estamos sobreescribiendo el método de inserción per sé vamos a comentar la línea de código que llama el hook.
			//$this->hook_before_add($this->arr);

			//manipulando información antes del insert.
			//return Request::all();
			$detalles_id_item = Request::all('detalles-id_item');
			$detalles_precio = Request::all('detalles-precio');
			$detalles_qty = Request::all('detalles-qty');
			$detalles_subtotal = Request::all('detalles-subtotal');
			$total = Request::all('total');
			$saldo = Request::all('saldo');
			$id_mesa = Request::input('id_mesa');
			$mesa = Tables::find($id_mesa);
			$mesa->ocupada = 1;
			for($i=0;$i<count($detalles_id_item['detalles-id_item']); $i++){
				
				$item = Items::find($detalles_id_item['detalles-id_item'][$i]);			
				if($item->is_recipe == 0){
					if($item->stock < $detalles_qty['detalles-qty'][$i]){
						$detalles_qty['detalles-qty'][$i] = $item->stock;
					}
					$item->stock -= $detalles_qty['detalles-qty'][$i];
					//aqui disminuimos el stock del item;
					$item->save();
				}
				if ($item->is_recipe==1){					
					$receta = Recipes::where('id_item',($detalles_id_item['detalles-id_item'][$i]))->get();
					foreach($receta as $rec){
						$ingredient = Items::find($rec->id_ingredient);
						$item_usage = ItemsUsage::firstOrNew(['id_item'=>$rec->id_ingredient], ['tragos_restantes'=>0]);
						$item_usage->save();
						$total_volumen = $detalles_qty['detalles-qty'][$i] * $rec->tragos;
						if($total_volumen > ($ingredient->stock * $ingredient->tragos_por)+$item_usage->tragos_restantes){
							$this->return_url = ($this->return_url) ? $this->return_url : Request::get('return_url');
							CRUDBooster::redirect($this->return_url, ("Inventario insuficiente para preparar $item->nombre x ".$detalles_qty['detalles-qty'][$i]), 'danger');
						}
					}
					foreach($receta as $rec){
						$ingredient = Items::find($rec->id_ingredient);
						$item_usage = ItemsUsage::firstOrNew(['id_item'=>$rec->id_ingredient], ['tragos_restantes'=>0]);
						$item_usage->save();
						$total_volumen = $detalles_qty['detalles-qty'][$i] * $rec->tragos;
						$item_usage->tragos_restantes -= $total_volumen;
						while($item_usage->tragos_restantes < 0){
							$item_usage->tragos_restantes += $ingredient->tragos_por;
							$ingredient->stock--;
						}
						
								
						$item_usage->save();
						$ingredient->save();
					}
				}
				$detalles_subtotal['detalles-subtotal'][$i] = $detalles_qty['detalles-qty'][$i] * $item->precio_venta;
			}
			//$total['total'] = array_sum($detalles_subtotal['detalles-subtotal']);
			//$saldo['saldo'] = $total['total'];
			$x = array_sum($detalles_subtotal['detalles-subtotal']);
			Request::merge($detalles_qty);
			Request::merge($detalles_subtotal);
			//Request::merge($total);
			//Request::merge($saldo);
			$mesa->save();
			//$this->arr[$this->primary_key] = $id = CRUDBooster::newId($this->table); //error on sql server
			$lastInsertId = $id = DB::table($this->table)->insertGetId($this->arr);
			$my_order = Orders::find($lastInsertId);
			$my_order->total = $x;
			$my_order->saldo = $x;
			$my_order->save();
			//Looping Data Input Again After Insert
			foreach ($this->data_inputan as $ro) {
				$name = $ro['name'];
				if (! $name) {
					continue;
				}

				$inputdata = Request::get($name);

				//Insert Data Checkbox if Type Datatable
				if ($ro['type'] == 'checkbox') {
					if ($ro['relationship_table']) {
						$datatable = explode(",", $ro['datatable'])[0];
						$foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
						$foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
						DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

						if ($inputdata) {
							$relationship_table_pk = CB::pk($ro['relationship_table']);
							foreach ($inputdata as $input_id) {
								DB::table($ro['relationship_table'])->insert([
												//$relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
												$foreignKey => $id,
												$foreignKey2 => $input_id,
											]);
										}
									}
								}
							}

							if ($ro['type'] == 'select2') {
								if ($ro['relationship_table']) {
									$datatable = explode(",", $ro['datatable'])[0];
									$foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
									$foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
									DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

									if ($inputdata) {
										foreach ($inputdata as $input_id) {
											$relationship_table_pk = CB::pk($row['relationship_table']);
											DB::table($ro['relationship_table'])->insert([
										//$relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
									$foreignKey => $id,
									$foreignKey2 => $input_id,
								]);
							}
						}
					}
				}

				if ($ro['type'] == 'child') {
					$name = str_slug($ro['label'], '');
					$columns = $ro['columns'];
					$getColName = Request::get($name.'-'.$columns[0]['name']);
					$count_input_data = ($getColName)?(count($getColName) - 1):0;
					$child_array = [];

					for ($i = 0; $i <= $count_input_data; $i++) {
						$fk = $ro['foreign_key'];
						$column_data = [];
						$column_data[$fk] = $id;
						foreach ($columns as $col) {
							$colname = $col['name'];
							$column_data[$colname] = Request::get($name.'-'.$colname)[$i];
						}
						$child_array[] = $column_data;
					}

					$childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
					DB::table($childtable)->insert($child_array);
				}
			}
			//Dado que estamos modificando el método de inserción se remueve el hook, en cambio el código que se haya de insertar despues de la inserción va aquí.
			//$this->hook_after_add($lastInsertId);

			
			
			$this->return_url = ($this->return_url) ? $this->return_url : Request::get('return_url');

			//insert log
			CRUDBooster::insertLog(trans("crudbooster.log_add", ['name' => $this->arr[$this->title_field], 'module' => CRUDBooster::getCurrentModule()->name]));

			if ($this->return_url) {
				if (Request::get('submit') == trans('crudbooster.button_save_more')) {
					CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans("crudbooster.alert_add_data_success"), 'success');
				} else {
					CRUDBooster::redirect($this->return_url, trans("crudbooster.alert_add_data_success"), 'success');
				}
			} else {
				if (Request::get('submit') == trans('crudbooster.button_save_more')) {
					CRUDBooster::redirect(CRUDBooster::mainpath('add'), trans("crudbooster.alert_add_data_success"), 'success');
				} else {
					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_data_success"), 'success');
				}
			}
			
			
		}

		public function postEditSave($id)
		{
			$this->cbLoader();
			$row = DB::table($this->table)->where($this->primary_key, $id)->first();
	
			if (! CRUDBooster::isUpdate() && $this->global_privilege == false) {
				CRUDBooster::insertLog(trans("crudbooster.log_try_add", ['name' => $row->{$this->title_field}, 'module' => CRUDBooster::getCurrentModule()->name]));
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}
	
			$this->validation($id);
			$this->input_assignment($id);
	
			if (Schema::hasColumn($this->table, 'updated_at')) {
				$this->arr['updated_at'] = date('Y-m-d H:i:s');
			}
	
			//$this->hook_before_edit($this->arr, $id);
			return $this->form;
			DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);
	
			//Looping Data Input Again After Insert
			foreach ($this->data_inputan as $ro) {
				$name = $ro['name'];
				if (! $name) {
					continue;
				}
	
				$inputdata = Request::get($name);
	
				//Insert Data Checkbox if Type Datatable
				if ($ro['type'] == 'checkbox') {
					if ($ro['relationship_table']) {
						$datatable = explode(",", $ro['datatable'])[0];
	
						$foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
						$foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
						DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();
	
						if ($inputdata) {
							foreach ($inputdata as $input_id) {
								$relationship_table_pk = CB::pk($ro['relationship_table']);
								DB::table($ro['relationship_table'])->insert([
	//                                 $relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
									$foreignKey => $id,
									$foreignKey2 => $input_id,
								]);
							}
						}
					}
				}
	
				if ($ro['type'] == 'select2') {
					if ($ro['relationship_table']) {
						$datatable = explode(",", $ro['datatable'])[0];
	
						$foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
						$foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
						DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();
	
						if ($inputdata) {
							foreach ($inputdata as $input_id) {
								$relationship_table_pk = CB::pk($ro['relationship_table']);
								DB::table($ro['relationship_table'])->insert([
	//                                 $relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
									$foreignKey => $id,
									$foreignKey2 => $input_id,
								]);
							}
						}
					}
				}
	
				if ($ro['type'] == 'child') {
					$name = str_slug($ro['label'], '');
					$columns = $ro['columns'];
					$getColName = Request::get($name.'-'.$columns[0]['name']);
					$count_input_data = ($getColName)?(count($getColName) - 1):0;
					$child_array = [];
					$childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
					$fk = $ro['foreign_key'];
	
					DB::table($childtable)->where($fk, $id)->delete();
					$lastId = CRUDBooster::newId($childtable);
					$childtablePK = CB::pk($childtable);
	
					for ($i = 0; $i <= $count_input_data; $i++) {
	
						$column_data = [];
						$column_data[$childtablePK] = $lastId;
						$column_data[$fk] = $id;
						foreach ($columns as $col) {
							$colname = $col['name'];
							$column_data[$colname] = Request::get($name.'-'.$colname)[$i];
						}
						$child_array[] = $column_data;
	
						$lastId++;
					}
	
					$child_array = array_reverse($child_array);
	
					DB::table($childtable)->insert($child_array);
				}
			}
	
			$this->hook_after_edit($id);
	
			$this->return_url = ($this->return_url) ? $this->return_url : Request::get('return_url');
	
			//insert log
			$old_values = json_decode(json_encode($row), true);
			CRUDBooster::insertLog(trans("crudbooster.log_update", [
				'name' => $this->arr[$this->title_field],
				'module' => CRUDBooster::getCurrentModule()->name,
			]), LogsController::displayDiff($old_values, $this->arr));
	
			if ($this->return_url) {
				CRUDBooster::redirect($this->return_url, trans("crudbooster.alert_update_data_success"), 'success');
			} else {
				if (Request::get('submit') == trans('crudbooster.button_save_more')) {
					CRUDBooster::redirect(CRUDBooster::mainpath('add'), trans("crudbooster.alert_update_data_success"), 'success');
				} else {
					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_update_data_success"), 'success');
				}
			}
		}
	

	}