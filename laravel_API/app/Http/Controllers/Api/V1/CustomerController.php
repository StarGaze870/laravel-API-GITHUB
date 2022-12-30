<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use App\Models\Invoice;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\V1\UpdateCustomerRequest;
use App\Http\Requests\V1\DeleteCustomerRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\CustomerCollection;
use App\Filters\V1\CustomerFilters;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = new CustomerFilters();
        $filterItems = $filter->transform($request); // [['column', 'operator', 'value']]
        
        $includeInvoices = $request->query('includeInvoices');                

        // \Log::info($includeInvoices);
        // \Log::info(gettype($includeInvoices));       
        // \Log::info($request);
        // \Log::error('Something is really going wrong.');

        $customers = Customer::where($filterItems);

        if (strcmp($includeInvoices, 'true') == 0) {
            $customers = $customers->with('invoices');            
        }        
        else if (strcmp($includeInvoices, 'false') == 0) {
            
            return new CustomerCollection(Invoice::rightJoin('customers', 'customers.id', '=', 'invoices.customer_id')->where('invoices.customer_id', '=', null)->paginate()->appends($request->query()));
            // $customers = $customers->without('invoices');            
        }        
        // $test = new CustomerCollection();
        return new CustomerCollection($customers->paginate()->appends($request->query()));        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        return new CustomerResource(Customer::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $includeInvoices = request()->query('includeInvoices');                

        if ($includeInvoices) {
            return new CustomerResource($customer->LoadMissing('invoices'));
        }
        return new CustomerResource($customer);
    } 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteCustomerRequest $customer)
    {
        $customer->delete();
    }
}
