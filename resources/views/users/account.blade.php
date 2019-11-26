@extends('Layouts.frontLayout.front_design')
@section('content')

<section id="form" style="margin-top:20px;"><!--form-->
<div class="container">
<div class="row">
	@if(Session::has('flash_message_error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
                <strong> {!! session('flash_message_error') !!} </strong>
        </div>
    @endif
    @if(Session::has('flash_message_success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
                <strong> {!! session('flash_message_success') !!} </strong>
        </div>
    @endif
	<div class="col-sm-4 col-sm-offset-1">
		<div class="login-form"><!--login form-->
			<h2>Update Account</h2>
			<form id="accountForm" name="accountForm" action="{{ url('/account') }}" method="post"> @csrf
				<div class="form-group">
					<input class="form-control" value="{{ $userDetails->name }}" id="name" name="name" type="text" placeholder="Name"/>
				</div>
				<div class="form-group">
					<input class="form-control" value="{{ $userDetails->address }}" id="address" name="address" type="text" placeholder="Address"/>
				</div>
				<div class="form-group">
					<input class="form-control" value="{{ $userDetails->city }}" id="city" name="city" type="text" placeholder="City"/>
				</div>
				<div class="form-group">
					<input class="form-control" value="{{ $userDetails->state }}" id="state" name="state" type="text" placeholder="State"/>
				</div>
				<div class="form-group">
					<select class="form-control" id="country" name="country">
						<option value="">Select Country</option>
						@foreach($countries as $country)
							<option value="{{ $country->country_name }}" @if($country->country_name == $userDetails->country) selected @endif  >{{ $country->country_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<input value="{{ $userDetails->pincode }}" class="form-control" id="pincode" name="pincode" type="text" placeholder="Pincode"/>
				</div>
				<div class="form-group">
					<input value="{{ $userDetails->mobile }}" class="form-control" id="mobile" name="mobile" type="text" placeholder="Mobile"/>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-default">Update</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-sm-1">
		<h2 class="or">OR</h2>
	</div>
	<div class="col-sm-4">
		<div class="signup-form">
			<h2>Update Password</h2>
			<form id="passwordForm" name="passwordForm" action="{{ url('/update-user-pwd') }}" method="post"> @csrf
				<input type="password" name="current_pwd" id="current_pwd" placeholder="Current Password">
				<span id="chkPwd"></span>
				<input type="password" name="new_pwd" id="new_pwd" placeholder="New Password">
				<input type="password" name="confirm_pwd" id="confirm_pwd" placeholder="Confirm Password">
				<button type="submit" class="btn btn-default">Update</button>
			</form>
		</div>
	</div>
</div>
</div>
</section><!--/form-->

@endsection