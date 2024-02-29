<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use App\Models\StoreAdmin\Tax;
use App\Models\StoreAdmin\TaxHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TaxController extends Controller
{
    protected $store_url,$store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }
    
    public function index()
    {
        //
    }

    public function create()
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $tax_details = Tax::where('store_id',Auth::user()->store_id)->get(['tax_percentage','tax_id']);
        $tax_history_details = TaxHistory::where('store_id',Auth::user()->store_id)
            ->select('old_tax_value','new_tax_value')
            ->selectRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as tax_created_at")
            ->orderBy("tax_history_id","desc")
            ->get();
        return view('store_admin.tax.list',compact('store_url','store_logo','tax_details','tax_history_details'));
    }

    public function store(Request $request)
    {
        $tax = []; $tax_history = [];
        $tax['tax_percentage'] = $tax_history['new_tax_value'] = $request->tax_percentage;
        $tax_history['old_tax_value'] = $request->old_tax_value;
        $tax['store_id'] = $tax_history['store_id'] = Auth::user()->store_id;
        $tax_history['created_by'] =  Auth::user()->id;
        if(!empty($request->tax_id)) {
            $tax_id = Crypt::decrypt($request->tax_id);
            $tax['updated_by'] = Auth::user()->id;
            Tax::where('tax_id',$tax_id)->update($tax);
        } else {
            $tax['created_by'] = Auth::user()->id;
            Tax::create($tax);
        }
        TaxHistory::create($tax_history);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.tax.create')->with('message',"Tax updated successfully..!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
