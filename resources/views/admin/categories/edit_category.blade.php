@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/admin/add-category') }}" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Categories</a> <a href="#" class="current">Edit Category</a> </div>
    <h1>Categories</h1>
  </div>
  <div class="container-fluid"><hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Add Category</h5>
          </div>
          <div class="widget-content nopadding">
            <form class="form-horizontal" method="post" action="{{ url('/admin/edit-category/'.$categoryDetails->id) }}" name="edit_category" id="edit_category" novalidate="novalidate"> @csrf
              <div class="control-group">
                 <label class="control-label">Category Name</label>
                 <div class="controls">
                  <input type="text" name="category_name" id="category_name" value="{{ $categoryDetails->name }}">
                </div>
              </div>
              <div class="control-group">
                 <label class="control-label">Category Level</label>
                 <div class="controls">
                   <select name="parent_id"  style="width: 220px;">
                 		<option value="0">Main Category</option>
                 		@foreach($levels as $val)
							<option value="{{ $val->id }}" @if($val->id == $categoryDetails->parent_id) selected @endif > {{ $val->name }}</option>
						@endforeach
					</select>
                </div>
              </div>
              <div class="control-group">
                 <label class="control-label">Descripton</label>
                 <div class="controls">
                  <input type="text" name="description" id="description" value="{{ $categoryDetails->description }}">
                </div>
              </div>
              <div class="control-group">
                 <label class="control-label">URL</label>
                 <div class="controls">
                  <input type="text" name="url" id="url" value="{{ $categoryDetails->url }}">
                </div>
              </div>
              <div class="control-group">
                 <label class="control-label">Enable</label>
                 <div class="controls">
                  <input type="checkbox" name="status" id="status" @if($categoryDetails->status == "1") checked @endif value="1">
                </div>
              </div>
              <div class="form-actions">
                <input type="submit" value="Edit Category" class="btn btn-success">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection