@extends('layouts.app')
@section('title', 'birthdayform')
@section('content')

    <main class="app-content">
        <section class="vh-100 bg-image"
            style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img4.webp');">
            <div class="mask d-flex align-items-center h-100 gradient-custom-3">
                <div class="container h-100">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                            <div class="card" style="border-radius: 15px;">
                                <div class="card-body p-5">
<h2 style="background: linear-gradient(to right, #00ffcc, #0099ff); -webkit-background-clip: text; color: transparent; text-align: center; font-size: 24px; margin-bottom: 15px;">ADD EMPLOYEE BIRTHDAY DETAILS</h2>
                                    <form method="post" action="{{ route('employeeformpost') }}">
                                        @if ($errors->any())
                                            <ul class="list-group">
                                                @foreach ($errors->all() as $error)
                                                    <li class=" alert alert-danger" style="list-style-type: none">
                                                        {{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @csrf
                                        @method('post')
                                        {{-- <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example1cg">Employee Name</label>
                                            <input type="text" id="form3Example1cg" class="form-control form-control-lg"
                                                placeholder="Enter NAME" name="ename" />
                                        </div> --}}

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example1cg">Select Employee</label>
                                            <select id="form3Example1cg" class="form-control form-control-lg"
                                                name="ename">
                                                <option value="" disabled selected>Select Employee</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example3cg">Employee Id</label>
                                            <input type="text" id="form3Example3cg" class="form-control form-control-lg"
                                                placeholder="Enter ID" name="eid" />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example4cg">Employee Date of Birth</label>
                                            <input type="date" id="form3Example4cg" class="form-control form-control-lg"
                                                placeholder="Enter D.O.B" name="edob" />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example4cg">Employee Role</label>
                                            <input type="text" id="form3Example4cg" class="form-control form-control-lg"
                                                placeholder="Enter ROLE" name="erole" />
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="submit"
                                                class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Add
                                                Employee Birthday</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

@endsection
