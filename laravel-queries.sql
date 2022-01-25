
App\Models\Customer::where('user_id',1)->get();

App\Models\Customer::where('user_id',1)->insurances()->get();

DB::table('customers')->where('customers.user_id', '=', 1)->where('customers.id', '=', 7)->join('insurances', 'customers.id', '=', 'insurances.customer_id')->get()		

DB::table('insurances')->join('customers', 'customers.id', '=', 'insurances.customer_id')->where('customers.user_id', '=', 1)->where('customers.id', '=', 7)->get()

$sigortaGroupList = $db_ext->table('sigorta')
    ->selectRaw('count(tc_no) as number_of_tc_no, tc_no')
    ->groupBy('tc_no')
    ->get();


$insDetails = DB::table('customers')
	->select('customers.*','customers.id as customerId','animals.*','animals.id as animalId','inseminations.*','inseminations.id as inseminationId')
	->where('customers.vet_id', '=', Auth::user()->vet->id)
	->join('animals', 'customers.id', '=', 'animals.customer_id')
	->join('inseminations', 'animals.id', '=', 'inseminations.animal_id')	
	->orderBy('inseminations.ins_date', 'ASC')->get();

$customersAnimals = DB::table('customers')->select('customers.*', 'animals.*', 'animals.id as animalId', 'customers.id as customerId')
			->where('customers.vet_id', '=', Auth::user()->vet->id)
			->join('animals', 'customers.id', '=', 'animals.customer_id')							
			->where('animals.ear_no', 'LIKE', '%'.$postData["searchWord"].'%')
			->orderBy('customers.name_surname', 'ASC')->get();
		
$insDetails = DB::table('customers')
	->select('customers.*','customers.id as customerId','animals.*','animals.id as animalId','inseminations.*','inseminations.id as inseminationId')
	->where('customers.vet_id', '=', Auth::user()->vet->id)
	->join('animals', 'customers.id', '=', 'animals.customer_id')
	->join('inseminations', 'animals.id', '=', 'inseminations.animal_id')							
	->where(DB::raw('MONTH(inseminations.ins_date)'), '=', $postData["txtMonth"])
	->where(DB::raw('YEAR(inseminations.ins_date)'), '=', $postData["txtYear"])
	->orderBy('inseminations.ins_date', 'ASC')
	->orderBy('inseminations.document_no', 'ASC')->get();	
