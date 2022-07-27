<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\Insurance;
use App\Http\Resources\Customer as CustomerResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CustomerController extends BaseController
{
    public function getAll()
    {
        $userid = Auth::user()->id;
        $customers = Customer::where('user_id', $userid)->get();
        //$customers = Customer::all();
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers fetched.');
    }

    public function getId($id)
    {
        $customer = Customer::find($id);
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }
        return $this->sendResponse(new CustomerResource($customer), 'Customer fetched.');
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'first_name' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $userid = Auth::user()->id;
        $input["user_id"] = $userid;

        $customer = Customer::create($input);
        return $this->sendResponse(new CustomerResource($customer), 'Customer created.');
    }

    public function update1(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $customer = Customer::find($id);
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }

        $customer->first_name = $input['first_name'];
        $customer->last_name = $input['last_name'];
        $customer->save();

        return $this->sendResponse(new CustomerResource($customer), 'Post updated.');
    }

    public function delete1($id)
    {
        $customer = Customer::find($id);
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }
        $customer->delete();
        return $this->sendResponse([], 'Customer deleted.');
    }

    public function search($name)
    {
        return Customer::where('name', 'like', '%' . $name . '%')->get();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'methodName' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        switch ($request->input('methodName')) {
            case "SelectByColumns":
                return $this->SelectByColumns($request);
            case "SelectByKey":
                return $this->SelectByKey($request);
            case "Insert":
                return $this->Insert($request);
            case "Update":
                return $this->Update($request);
            case "Delete":
                return $this->Delete($request);
            case "SelectCustomerStatistics":
                return $this->SelectCustomerStatistics($request);
            case "TransferRecords":
                return $this->TransferRecords($request);
            default:
                return $this->sendError("Method not found!");
        }
    }

    private function SelectByColumns($request)
    {
        $userid = Auth::user()->id;
        $customers = Customer::where('user_id', $userid)->get(); 
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers fetched.');
    } 

    private function SelectByKey($request)
    {
        if (!$request->has(['data', 'data.id'])) {
            return $this->sendError('Data can not be null.');
        }        
        $userid = Auth::user()->id;
        $id = $request->input('data.id');
        $customer = Customer::where('id', $id)->where('user_id', $userid)->first();
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }
        return $this->sendResponse(new CustomerResource($customer), 'Customer fetched.');
    }

    private function Insert($request)
    {
        $userid = Auth::user()->id;

        $newCustomer = new Customer;
        $newCustomer->user_id = $userid;
        if ($request->has(['data.first_name'])) 
            $newCustomer->first_name = $request->input('data.first_name');
        if ($request->has(['data.last_name'])) 
            $newCustomer->last_name = $request->input('data.last_name');
        if ($request->has(['data.phone'])) 
            $newCustomer->phone = $request->input('data.phone');
        if ($request->has(['data.email'])) 
            $newCustomer->email = $request->input('data.email');
        if ($request->has(['data.tc_no'])) 
            $newCustomer->tc_no = $request->input('data.tc_no');
        if ($request->has(['data.status'])) 
            $newCustomer->status = $request->input('data.status');
        else
            $newCustomer->status = true;
        $newCustomer->save();

        return $this->sendResponse(new CustomerResource($newCustomer), 'Customer inserted.');
    }

    private function Update($request)
    {
        if (!$request->has(['data', 'data.id'])) {
            return $this->sendError('Data can not be null.');
        }        
        $userid = Auth::user()->id;
        $id = $request->input('data.id');
        $customer = Customer::where('id', $id)->where('user_id', $userid)->first();
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }
        if ($request->has(['data.first_name'])) 
            $customer->first_name = $request->input('data.first_name');
        if ($request->has(['data.last_name'])) 
            $customer->last_name = $request->input('data.last_name');
        if ($request->has(['data.phone'])) 
            $customer->phone = $request->input('data.phone');
        if ($request->has(['data.email'])) 
            $customer->email = $request->input('data.email');
        if ($request->has(['data.tc_no'])) 
            $customer->tc_no = $request->input('data.tc_no');
        if ($request->has(['data.status'])) 
            $customer->status = $request->input('data.status');
        $customer->save();

        return $this->sendResponse(new CustomerResource($customer), 'Customer updated.');
    }

    private function Delete($request)
    {
        if (!$request->has(['data', 'data.id'])) {
            return $this->sendError('Data can not be null.');
        }        
        $userid = Auth::user()->id;
        $id = $request->input('data.id');
        $customer = Customer::where('id', $id)->where('user_id', $userid)->first();
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }

        // TODO sigorta var mı kontrolü yapılmalı foregin key hatası veriryor.
        
        $customer->delete();

        return $this->sendResponse(new CustomerResource($customer), 'Customer deleted.');
    }

    private function SelectCustomerStatistics($request)
    {
        // try {

        //   let { userId, isActive, chartNo } = data;
        //   let matchStr = {
        //     userId: mongoose.Types.ObjectId(userId)
        //   };

        //   if (isActive != undefined) matchStr.isActive = isActive;

        //   Customer
        //     .aggregate([
        //       { $match: matchStr },
        //       {
        //         $group: {
        //           _id: '$userId',
        //           totalCount: { $sum: 1 }
        //         }
        //       },
        //     ])
        //     .then((customers) => {
        //       if (customers.length > 0) {
        //         return apiResponse.successResponseWithData(res, apiResponse.Success, customers);
        //       } else {
        //         return apiResponse.successResponseWithData(res, apiResponse.Success, []);
        //       }
        //     });
        // } catch (err) {
        //   return apiResponse.ErrorResponse(res, err);
        // }

        // {
        //     "status": true,
        //     "message": "İşlem başarılı",
        //     "data": [
        //       {
        //         "_id": "5fccaa2c9065c724ec82d0ca",
        //         "totalCount": 745
        //       }
        //     ]
        // }
    }

    private function TransferRecords($request)
    {
        $userid = Auth::user()->id;

        $resDeleteInsurance = \DB::table('insurances')
            ->join('customers', 'customers.id', '=', 'insurances.customer_id')
            ->where('customers.user_id', '=', $userid) 
            ->delete();

        $resDeleteCustomer = Customer::where('user_id', $userid)->delete();


        $db_ext = \DB::connection('pgsql_external');  
        $sigortaList = $db_ext->table('sigorta')->limit(10000)->get(); 

        $response['countList'] = count($sigortaList);        
        $userid = Auth::user()->id;

        foreach($sigortaList as $sigItem)
        {
            $customer = Customer::where('tc_no', $sigItem->tc_no)->first();
            //$response['customer'] = $customer;
            $end_date = new Carbon($sigItem->baslangic);
            $insurance = new Insurance();
            $insurance->status = $sigItem->durumu;
            $insurance->start_date = $sigItem->baslangic;
            $insurance->end_date = $end_date->addYear(); 
            $insurance->plate_no = $sigItem->plaka;
            $insurance->car_register_no = $sigItem->tescil_no;
            $insurance->company = $sigItem->sirket;
            $insurance->policy_no = $sigItem->police_no;
            $insurance->description = $sigItem->aciklama;
            $insurance->commission_rate = $sigItem->oran;
            $insurance->gross_price = $sigItem->brut;
            $insurance->net_price = $sigItem->net;
            $insurance->commission_price = $sigItem->komisyon; 

            if (is_null($customer)) {
                $customer = new Customer();
                $customer->user_id = $userid;
                $customer->first_name = $sigItem->adi;
                $customer->last_name = $sigItem->soyadi;
                $customer->phone = str_replace(' ', '', $sigItem->telefon);
                $customer->tc_no = $sigItem->tc_no; 
                $customer->save();
                $customer->insurances()->save($insurance);
            }
            else { 
                $customer->insurances()->save($insurance);
            }
        }

        return $this->sendResponse($response, 'Transfer successful.');
    }
}
