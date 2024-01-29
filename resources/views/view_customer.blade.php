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
    <td>{{isset($view_customer_details[0]->project_name)?$view_customer_details[0]->project_name:''}}</td>
  </tr>
  <tr>
    <td><b>Customer Name</b></td>
    <td>{{isset($view_customer_details[0]->customer_name)?$view_customer_details[0]->customer_name:''}}</td>
  </tr>
  <tr>
    <td><b>Customer Email</b></td>
    <td>{{isset($view_customer_details[0]->customer_email)?$view_customer_details[0]->customer_email:''}}</td>
  </tr>
  <tr>
    <td><b>Customer Mobile</b></td>
    <td>{{isset($view_customer_details[0]->customer_mobile)?$view_customer_details[0]->customer_mobile:''}}</td>
  </tr>
  <tr>
    <td><b>Customer Designation</b></td>
    <td>{{isset($view_customer_details[0]->customer_designation)?$view_customer_details[0]->customer_designation:''}}</td>
  </tr>
  <tr>
    <td><b>Project Members</b></td>
    <td>{{isset($view_customer_details[0]->employee_list)?$view_customer_details[0]->employee_list:''}}</td>
  </tr>
   <tr>
    <td><b>Division</b></td>
    <td>{{isset($view_customer_details[0]->division)?$view_customer_details[0]->division:''}}</td>
  </tr>
   <tr>
    <td><b>Start Date</b></td>
    <td>{{isset($view_customer_details[0]->start_date)?$view_customer_details[0]->start_date:''}}</td>
  </tr>
  <tr>
    <td><b>Pan Number</b></td>
    <td>{{isset($view_customer_details[0]->pan_number)?$view_customer_details[0]->pan_number:''}}</td>
  </tr>
  <tr>
    <td><b>Gst Number</b></td>
    <td>{{isset($view_customer_details[0]->gst_number)?$view_customer_details[0]->gst_number:''}}</td>
  </tr>
  <?php

  $customer_id = isset($view_customer_details[0]->id)?$view_customer_details[0]->id:'';

   ?>
  <tr>
     <td><b>Action</b></td>
     <td><a href="{{ url('edit_company_details/'.$customer_id) }}" class="btn btn-primary">Update</a></td>
  </tr>

</table>

  <div class="mt-3">
   <center><h3>Primary Contact</h3></center>
    <table class="mt-3" style="border:0">
      <tr>
        <td><b>Primary Contact Name</b></td>
        <td>{{$view_primary_contact->contact_name ?? ''}}</td>
      </tr>
      <tr>
        <td><b>Primary Contact Email</b></td>
        <td>{{$view_primary_contact->contact_email ?? ''}}</td>
      </tr>
      <tr>
        <td><b>Primary Contact Mobile</b></td>
        <td>{{$view_primary_contact->contact_mobile ?? '' }}</td>
      </tr>
      <tr>
        <td><b>Primary Contact Designation</b></td>
        <td>{{$view_primary_contact->contact_designation ?? ''}}</td>
      </tr>
        @isset($view_primary_contact->id)
        <tr>
            <td><b>Action</b></td>
            <td><a href="{{ url('edit_primary_contact/'.$view_primary_contact->id) }}" class="btn btn-primary">Update</a></td>
            </tr>
        @endisset
    </table>
  </div>
  <?php
 // echo "<pre>";print_r($view_sub_contacts);exit;
   if(isset($view_sub_contacts))
   {
  ?>

  <div class="mt-3">
   <center><h3>Sub Contacts</h3></center>
    <table class="mt-3" style="border:0">
      <?php ?>
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
<?php } ?>

</div>
</div>
</main>



@endsection
