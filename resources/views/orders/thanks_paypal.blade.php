@extends('layouts.frontLayout.front_design')
@section('content')

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
			<h3>YOUR PAYPAL ORDER HAS BEEN PLACED</h3>
			<p>Thanks for payment. We will process your order very soon</p>
			<p>Your order number is {{ Session::get('order_id') }} and total amount paid is US {{ Session::get('grand_total') }}</p>
		</div>
	</div>
</section>
@endsection
<?php 
Session::forget('grand_total');
Session::forget('order_id');
?>