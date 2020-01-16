@extends('layouts.global')
@section('title') Category List @endsection
@section('content')
<h1>Category List</h1>

<div class="row">
    <div class="col-md-6">
        <form action="{{route('categories.index')}}">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Filter by category name" value="{{Request::get('name')}}" name="name">
                <div class="input-group-append">
                    <input type="submit" value="Filter" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
       
    <div class="col-md-6">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link active" href=" {{route('categories.index')}}">Published</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href=" {{route('categories.trash')}}">Trash</a>
            </li>
        </ul>
    </div>
       
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <a href="{{route('categories.create')}}" class="btn btn-primary">Create category</a>
    </div>
</div>
   <br>

   @if(session('status'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                {{session('status')}}
            </div>
        </div>
    </div>
  @endif
  <br>
  
<table class="table table-bordered">
    <thead>
        <tr>
            <th><b>Name</b></th>
            <th><b>Slug</b></th>
            <th><b>Image</b></th>
            <th><b>Action</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($category as $c)
            <tr>
                <td>{{$c->name}}</td>
                <td>{{$c->slug}}</td>
                <td>
                    @if($c->image)
                    <img src="{{asset('storage/'.$c->image)}}" width="70px"/>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <a class="btn btn-info text-white btn-sm" href="{{route('categories.edit', [$c->id])}}">Edit</a>
                    <form onsubmit="return confirm('Pindahkan Ke Tempat Sampah ?')" class="d-inline" action="{{route('categories.destroy', [$c->id])}}" method="POST">
                    @csrf
                        <input type="hidden" name="_method" value="DELETE"> 
                        <input type="submit" value="Trash" class="btn btn-danger btn-sm">
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan=10>
                
            </td>
        </tr>
    </tfoot>       
</table>
@endsection
