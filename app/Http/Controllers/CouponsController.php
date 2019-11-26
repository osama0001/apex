<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;

class CouponsController extends Controller
{
    public function addCoupon(Request $request){
    	if($request->isMethod('post')){
    		$data = $request->all();
    		$coupon = new Coupon;
    		$coupon->coupon_code = $data['coupon_code'];
    		$coupon->amount = $data['amount'];
    		$coupon->amount_type = $data['amount_type'];
    		$coupon->expiry_date = $data['expiry_date'];
    		if(!empty($data['status'])){
    			$coupon->status = $data['status'];
    		}else{
    			$coupon->status = 0;
    		}
    		$coupon->save();
    		return redirect()->action('CouponsController@viewCoupons')->with('flash_message_success','Coupon has updated Successfully!');
    	}
    	return view('admin.coupons.add_coupon');
    }

    public function editCoupon(Request $request,$id = null){

    	if($request->isMethod('post')){
    		$data = $request->all();
    		$coupon = Coupon::find($id);
    		$coupon->coupon_code = $data['coupon_code'];
    		$coupon->amount = $data['amount'];
    		$coupon->amount_type = $data['amount_type'];
    		$coupon->expiry_date = $data['expiry_date'];
    		if(!empty($data['status'])){
    			$coupon->status = $data['status'];
    		}else{
    			$coupon->status = 0;
    		}
    		$coupon->save();
    		return redirect()->action('CouponsController@viewCoupons')->with('flash_message_success','Coupon has updated Successfully!');
    	}
    	$couponDetails = Coupon::find($id);
    	return view('admin.coupons.edit_coupon')->with(compact('couponDetails'));
    }

    public function viewCoupons(){
    	$coupons = Coupon::get();
    	return view('admin.coupons.view_coupons')->with(compact('coupons'));
    }

    public function deleteCoupon($id){
    	Coupon::where(['id'=>$id])->delete();
    	return redirect()->back()->with('flash_message_success','Coupon has been deleted Successfully!');
    }
}
