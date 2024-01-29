@extends('layouts.app')
<style>
table {  
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 10px;
}

</style>
@section('content')
<main class="app-content">
<div class="container-fluid">
<div class="col-md-12 user-right">
  <div class="panel panel-default">
      <div class="panel-heading mt-3">
      </div>
  </div>
   
   <center><h3>Company Details</h3></center>
  <table class="mt-3" style="border:0">
  <tr> 
    <td><b>Project Name</b></td>
    <td>{{$view_customer_details->project_name}}</td>    
  </tr>
  <tr>
    <td><b>Customer Name</b></td>
    <td>{{$view_customer_details->customer_name}}</td>    
  </tr>
  <tr>
    <td><b>Customer Email</b></td>
    <td>{{$view_customer_details->customer_email}}</td>    
  </tr>
  <tr>
    <td><b>Customer Mobile</b></td>
    <td>{{$view_customer_details->customer_mobile}}</td>    
  </tr>
  <tr>
    <td><b>Customer Designation</b></td>
    <td>{{$view_customer_details->customer_designation}}</td>    
  </tr>
  
   <tr>
    <td><b>Division</b></td>
    <td>{{$view_customer_details->division}}</td>   
  </tr>
   <tr>
    <td><b>Start Date</b></td>
    <td>{{$view_customer_details->start_date}}</td>   
  </tr>
  <tr>
    <td><b>Pan Number</b></td>
    <td>{{$view_customer_details->pan_number}}</td>   
  </tr>
  <tr>
    <td><b>Gst Number</b></td>
    <td>{{$view_customer_details->gst_number}}</td>   
  </tr>
  <tr>
     <td><b>Action</b></td>
     <td><a href="{{ url('edit_company_details/'.$view_customer_details->id) }}" class="btn btn-primary">Update</a></td>
  </tr>
   
</table>
  
  <div class="mt-3">
   <center><h3>Primary Contact</h3></center>
    <table class="mt-3" style="border:0">
      <tr> 
        <td><b>Primary Contact Name</b></td>
        <td>{{$view_primary_contact->contact_name}}</td>    
      </tr>
      <tr>
        <td><b>Primary Contact Email</b></td>
        <td>{{$view_primary_contact->contact_email}}</td>    
      </tr>
      <tr>
        <td><b>Primary Contact Mobile</b></td>
        <td>{{$view_primary_contact->contact_mobile}}</td>    
      </tr>
      <tr>
        <td><b>Primary Contact Designation</b></td>
        <td>{{$view_primary_contact->contact_designation}}</td>    
      </tr>
      <tr>
       <td><b>Action</b></td>
       <td><a href="{{ url('edit_primary_contact/'.$view_primary_contact->id) }}" class="btn btn-primary">Update</a></td>
       </tr>     
    </table>
  </div>

  <div class="mt-3">
   <center><h3>Sub Contacts</h3></center>
    <table class="mt-3" style="border:0">
      
      <tr> 
        <td><b>Sub Contact Name</b></td>
        <td>{{$view_sub_contacts->contact_name}}</td>    
      </tr>
      <tr>
        <td><b>Sub Contact Email</b></td>
        <td>{{$view_sub_contacts->contact_email}}</td>    
      </tr>
      <tr>
        <td><b>Sub Contact Mobile</b></td>
        <td>{{$view_sub_contacts->contact_mobile}}</td>    
      </tr>
      <tr>
        <td><b>Sub Contact Designation</b></td>
        <td>{{$view_sub_contacts->contact_designation}}</td>    
      </tr> 
      <tr>
       <td><b>Action</b></td>
       <td><a href="{{ url('edit_sub_contacts/'.$view_sub_contacts->id) }}" class="btn btn-primary">Update</a></td>
      </tr>
    </table>
  </div>

</div>
</div>
</main>
 
 

@endsection