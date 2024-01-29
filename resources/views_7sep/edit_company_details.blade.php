@extends('layouts.app')

@section('content')
<main class="app-content">
<div class="container">
    <div class="row">
        <div class="col-md-12">
           @if (session('status'))
                <h6 class="alert alert-success">{{ session('status') }}</h6>
            @endif
            <div class="card">
                <div class="card-header">
                    <h4>Edit & Update Company Details
                        
                    </h4>
                </div>
                <div class="card-body">
             
                    <form action="{{ url('update_company_details/'.$details->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                       
                        <div class="form-group">
                          <label for="customer_email">Customer Email:</label>
                          <input type="email" class="form-control" id="customer_email" name="customer_email" value="{{$details->customer_email}}">
                        </div>
                        <div class="form-group">
                          <label for="customer_mobile">Customer Mobile No:</label>
                          <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" value="{{$details->customer_mobile}}">
                        </div>
                        <div class="form-group">
                          <label for="customer_designation">Customer Designation:</label>
                          <input type="text" class="form-control" id="customer_designation" name="customer_designation" value="{{$details->customer_designation}}">
                        </div>                       

                        <div class="form-group">
                          <label for="pan_number">Pan Number</label>
                          <input type="text" class="form-control" id="pan_number" name="pan_number" value="{{$details->pan_number}}">
                        </div>
                        <div class="form-group">
                          <label for="gst_number">Gst Number</label>
                          <input type="text" class="form-control" id="gst_number" name="gst_number" value="{{$details->gst_number}}">
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</main>

@endsection