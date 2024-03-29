@extends('layouts.frontLayout.front_design')
@section('content')
<?php use App\Order; ?>

<section id="cart_items">
	<div class="container">
		<div class="breadcrumbs">
			<ol class="breadcrumb">
			  <li><a href="#">Home</a></li>
			  <li class="active">Thanks</li>
			</ol>
		</div>
	</div>
</section> <!--/#cart_items-->

<section id="do_action">
	<div class="container">
		<div class="heading text-center">
			<h3>YOUR ORDER HAS BEEN PLACED</h3>
			<p>Your order number is {{ Session::get('order_id') }} and total payable about is US {{ Session::get('grand_total') }}</p>
			<p>Plase make payment by clicking on below payment button</p>
			<?php
			$orderDetails = Order::getOrderDetails(Session::get('order_id'));
			$orderDetails = json_decode(json_encode($orderDetails));
			// echo "<pre>"; print_r($orderDetails); die;
			$nameArr = explode(' ', $orderDetails->name);
			$getCountryCode = Order::getCountryCode($orderDetails->country);
			?>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			  <input type="hidden" name="cmd" value="_xclick">
			  <input type="hidden" name="business" value="sb-2yagk622289@business.example.com">
			  <input type="text" name="item_name" value="{{ Session::get('order_id') }}">
			  <input type="text" name="currency_code" value="USD">
			  <input type="text" name="amount" value="{{ Session::get('grand_total') }}">
			  <input type="text" name="first_name" value="{{ $nameArr[0] }}">
			  <input type="text" name="last_name" value="{{ $nameArr[1] }}">
			  <input type="text" name="address1" value="{{ $orderDetails->address }}">
			  <input type="text" name="address2" value="asdasd">
			  <input type="text" name="city" value="{{ $orderDetails->city }}">
			  <input type="text" name="state" value="{{ $orderDetails->state }}">
			  <input type="text" name="zip" value="{{ $orderDetails->pincode }}">
			  <input type="text" name="email" value="{{ $orderDetails->user_email }}">
			  <input type="text" name="country" value="{{ $getCountryCode->country_code }}">
			  <input type="hidden" name="return" value="{{ url('paypal/thanks') }}">
			  <input type="hidden" name="cancel_return" value="{{ url('paypal/cancel') }}">
			  <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_paynow_107x26.png" alt="Pay Now">
			  <img alt="" src="https://paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>
</section>
@endsection
<?php 
Session::forget('grand_total');
Session::forget('order_id');
?>