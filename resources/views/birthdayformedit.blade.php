@extends('layouts.app')

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
                                    <h2 class="text-uppercase text-center mb-5"
                                        style="background: linear-gradient(to right, #ff6a00, #09ee1c); color: #fff; padding: 10px; border-radius: 10px;">
                                        Edit Employee Birthday Details</h2>
                                    <form method="post" action="{{ route('employeeformpostput', ['id' => $id]) }}">
                                        @if ($errors->any())
                                            <ul class="list-group">
                                                @foreach ($errors->all() as $error)
                                                    <li class="list-group-item alert alert-success">{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @csrf
                                        @method('put')

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example1cg">Employee Name</label>
                                            <select id="form3Example1cg" class="form-control form-control-lg"
                                                name="ename">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->name }}" @if($user->name == $id->ename) selected @endif>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div> 

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example3cg">Employee Id</label>
                                            <input type="text" id="form3Example3cg" class="form-control form-control-lg"
                                                value="{{ $id->eid }}" name="eid" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example4cg">Employee Date of Birth</label>
                                            <input type="date" id="form3Example4cg" class="form-control form-control-lg"
                                                value="{{ $id->edob }}" name="edob" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="form3Example4cg">Employee Role</label>
                                            <input type="text" id="form3Example4cg" class="form-control form-control-lg"
                                                value="{{ $id->erole }}" name="erole" />
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            <button type="submit"
                                                class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Update
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
