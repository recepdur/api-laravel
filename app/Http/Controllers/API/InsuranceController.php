<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\Insurance;
use App\Http\Resources\Insurance as InsuranceResource;
   
class InsuranceController extends BaseController
{
    public function index()
    {
        $userid = Auth::user()->id;
        $customer = Customer::where('user_id',$userid)->where('id', '=', 1)->get();
        $insurances = Insurance::where('user_id',$userid)->get(); 
        return $this->sendResponse(InsuranceResource::collection($insurances), 'Insurances fetched.');
    }
    
    public function getAll($customer_id)
    {
        $userid = Auth::user()->id;
        $customer = Customer::where('user_id', $userid)->where('id', '=', $customer_id)->get();
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }
        $insurances =  $customer->insurances; 
        return $this->sendResponse(InsuranceResource::collection($insurances), 'Insurances fetched.');
    }

    public function getId($id)
    {
        $insurance = Insurance::find($id);
        if (is_null($insurance)) {
            return $this->sendError('Insurance does not exist.');
        }
        return $this->sendResponse(new InsuranceResource($insurance), 'Insurance fetched.');
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'start_date' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $userid = Auth::user()->id; 
        $input["user_id"] = $userid; 

        $insurance = Insurance::create($input);
        return $this->sendResponse(new InsuranceResource($insurance), 'Insurance created.');
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'start_date' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $insurance = Insurance::find($id);
        if (is_null($insurance)) {
            return $this->sendError('Insurance does not exist.');
        }
        
        $insurance->start_date = $input['start_date']; 
        $insurance->save();
        
        return $this->sendResponse(new InsuranceResource($insurance), 'Post updated.');
    }
   
    public function delete($id)
    { 
        $insurance = Insurance::find($id);
        if (is_null($insurance)) {
            return $this->sendError('Insurance does not exist.');
        }
        $insurance->delete();
        return $this->sendResponse([], 'Insurance deleted.');
    }

    public function search($name)
    {
        return Insurance::where('name', 'like', '%'.$name.'%')->get();
    }
}