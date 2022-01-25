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
        $input = $request->all();
        $validator = Validator::make($input, [
            'methodName' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $methodName = $input["methodName"];
        switch ($methodName) {
            case "SelectByColumns":
                return $this->SelectByColumns($input);
            case "SelectByKey":
                return $this->SelectByKey($input);
            case "Insert":
                return $this->Insert($input);
            case "Update":
                return $this->Update($input);
            case "Delete":
                return $this->Delete($input);
            case "SelectCustomerStatistics":
                return $this->SelectCustomerStatistics($input);
            case "TransferRecords":
                return $this->TransferRecords($input);
            default:
                return $this->sendError("Method not found!");
        }
    }

    private function SelectByColumns($input)
    {
        $userid = Auth::user()->id;
        $data = $input["data"]; 
        //$userid = $data["userId"]; 
        $customers = Customer::where('user_id', $userid)->get(); 
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers fetched.');

        // let { userId, _id, isActive, firstName } = data;
        // let matchStr = {
        //   userId: mongoose.Types.ObjectId(userId),
        // };

        // if (_id) matchStr._id = mongoose.Types.ObjectId(_id);
        // if (isActive != undefined) matchStr.isActive = isActive;
        // if (firstName) matchStr.firstName = new RegExp(firstName, 'i'); // insensitive   

        // Customer
        //   .aggregate([
        //     { $match: matchStr },
        //     { $sort: { firstName: 1 } }
        //   ])
        //   .then((customers) => {
        //     if (customers.length > 0) {
        //       return apiResponse.successResponseWithData(res, apiResponse.Success, customers);
        //     } else {
        //       return apiResponse.successResponseWithData(res, apiResponse.Success, []);
        //     }
        //   });

        // {
        //     "status": true,
        //     "message": "İşlem başarılı",
        //     "data": [
        //       {
        //         "_id": "5ff0b8cd39e97c14f0f56757",
        //         "isActive": true,
        //         "userId": "5fccaa2c9065c724ec82d0ca",
        //         "tcNo": "19826406590",
        //         "firstName": "AKadir123",
        //         "lastName": "Temel",
        //         "phone": "5462161670",
        //         "email": "",
        //         "created": "2021-01-16T20:17:39.688Z",
        //         "updated": "2021-01-16T20:47:47.000Z",
        //         "__v": 0
        //       },
    } 

    private function SelectByKey($input)
    {
        $customer = Customer::find($id);
        if (is_null($customer)) {
            return $this->sendError('Customer does not exist.');
        }
        return $this->sendResponse(new CustomerResource($customer), 'Customer fetched.');
        
        // const { _id } = data;
        // if (!mongoose.Types.ObjectId.isValid(_id)) {
        //   return apiResponse.validationErrorWithData(res, apiResponse.InvalidInfo, {});
        // }
        // Customer.findOne({ "_id": _id })
        //   .then((customer) => {
        //     if (customer !== null) {
        //       let customerData = new CustomerData(customer);
        //       return apiResponse.successResponseWithData(res, apiResponse.Success, customerData);
        //     } else {
        //       return apiResponse.successResponseWithData(res, apiResponse.Success, {});
        //     }
        //   });
    }

    private function Insert($input)
    {
        // const { userId, tcNo } = data;
        // if (!tcNo) {
        //   return apiResponse.validationErrorWithData(res, "TC No bilgisi boş olamaz!", {});
        // }
        // Customer.find({ 'userId': userId, 'tcNo': tcNo }, function (err, findTC) {
        //   if (findTC != null && findTC.length > 0) {
        //     return apiResponse.notFoundResponse(res, "TC No daha önce kullanılmış!");
        //   }
        //   var customer = new Customer({
        //     userId: data.userId,
        //     firstName: data.firstName,
        //     lastName: data.lastName,
        //     phone: data.phone,
        //     email: data.email,
        //     tcNo: data.tcNo,
        //   });
        //   customer.save(function (err) {
        //     if (err) {
        //       return apiResponse.ErrorResponse(res, err);
        //     }
        //     let customerData = new CustomerData(customer);
        //     return apiResponse.successResponseWithData(res, apiResponse.Success, customerData);
        //   });
        // });
    }

    private function Update($input)
    {

        //     const { userId, _id, tcNo } = data;
        //     if (!mongoose.Types.ObjectId.isValid(_id)) {
        //       return apiResponse.validationErrorWithData(res, apiResponse.InvalidInfo, {});
        //     }
        //     if (!tcNo) {
        //       return apiResponse.validationErrorWithData(res, "TC No bilgisi boş olamaz!", {});
        //     }

        //     Customer.findById(_id, function (err, findId) {
        //       if (findId === null) {
        //         return apiResponse.notFoundResponse(res, apiResponse.RecordNotFound);
        //       }
        //       Customer.find({ 'userId': userId, 'tcNo': tcNo }, function (err, findTC) {
        //         if (findTC != null && findTC.length > 0 && _id != findTC[0]._id) {
        //           return apiResponse.notFoundResponse(res, "TC No daha önce kullanılmış!");
        //         }

        //         var customerObj = new Customer(data);
        //         customerObj.updated = moment().format();

        //         Customer.findByIdAndUpdate(_id, customerObj, {}, function (err) {
        //           if (err) {
        //             return apiResponse.ErrorResponse(res, err);
        //           } else {
        //             let customerData = new CustomerData(customerObj);
        //             return apiResponse.successResponseWithData(res, apiResponse.Success, customerData);
        //           }
        //         });
        //       });
        //     });
    }

    private function Delete($input)
    {
        //     const { _id } = data;
        //     if (!mongoose.Types.ObjectId.isValid(_id)) {
        //       return apiResponse.validationErrorWithData(res, apiResponse.InvalidInfo, {});
        //     }
        //     Customer.findById(_id, function (err, foundCustomer) {
        //       if (foundCustomer === null) {
        //         return apiResponse.notFoundResponse(res, apiResponse.RecordNotFound);
        //       } else {
        //         Customer.findByIdAndRemove(_id, function (err) {
        //           if (err) {
        //             return apiResponse.ErrorResponse(res, err);
        //           } else {
        //             return apiResponse.successResponse(res, apiResponse.Success);
        //           }
        //         });
        //       }
        //     });
    }

    private function SelectCustomerStatistics($input)
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

    private function TransferRecords($input)
    {
        $userid = Auth::user()->id;
        $data = $input["data"]; 
        //$userid = $data["userId"]; 

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
