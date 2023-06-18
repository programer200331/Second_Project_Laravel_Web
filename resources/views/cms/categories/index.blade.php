@extends('cms.parent')

@section('title','Categories')

@section('page_name','Show Categories')

@section('content')
    <!-- Main content -->
<section class="content">
      <div class="container-fluid">
        <!-- /.row -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Categories Table</h3>

                <div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Info</th>
                      <th>Active</th>
                      <th>Created at</th>
                      <th>Updated at</th>
                      <th>Tools</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($data as $category)
                    <tr>
                      <td>{{$loop->index + 1}}</td>
                      <!-- <td>{{$category->id}}</td> -->
                      <td>{{$category->name}}</td>
                      <td>{{$category->info}}</td>
                      <td><span class="tag tag-success">{{$category->visibility}}</span></td>
                      <td>{{$category->created_at->diffForHumans()}}</td>
                      <td>{{$category->updated_at->diffForHumans()}}</td>
                      <td>
                      <div class="btn-group">
                        <a href="{{route('categories.edit',$category->id)}}" type="button" class="btn btn-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <Span style="width: 1px;"></Span>
                        <form action="{{route('categories.destroy',$category->id)}}" method="post">
                          @method('delete')
                          @csrf
                        <button type="submit" class="btn btn-danger">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                        </form>
                      </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection