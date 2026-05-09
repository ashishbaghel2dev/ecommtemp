@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')
<div class="main-content">

    <div class="top-bar">
        <h2 class="page-title">Users</h2>
        <p class="page-subtitle">Manage Users</p>
        <a href="#" class="btn-primary">
           Get Admin Details
        </a>
    </div>




   <div class="table-card">
        <table class="custom-table">
           
    <thead>
        <tr>
            <th>ID</th>
             <th>Avatar</th>
            <th>Name</th>
               <th>Phone</th>
            <th>Email</th>
               <th>Active / Inactive</th>
                  <th>Action </th>
        </tr>
    </thead>

    <tbody>
    

@foreach($users as $user)
<tr>
    <td>{{ $user->id }}</td>
<td>
    @if($user->avatar)

        <img 
            src="{{ $user->avatar }}" 
            alt="Avatar"
            width="40"
            height="40"
            style="border-radius:50%; object-fit:cover;"
        >

    @else

        <i class="fa-solid fa-user"></i>

    @endif
</td>
    <td>{{ $user->name }}</td>

<td>
    @if($user->phone)

        <span>
            {{ $user->phone }}
        </span>

        <span class="tag {{ $user->phone_verified_at ? 'verified' : 'unverified' }}">
            {{ $user->phone_verified_at ? 'Verified' : 'Unverified' }}
        </span>

    @else

        <span>-</span>

    @endif
</td>

<td>
    @if($user->email)

        <span>
            {{ $user->email }}
        </span>

        <span class="tag {{ $user->email_verified_at ? 'verified' : 'unverified' }}">
            {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
        </span>

    @else

        <span>-</span>

    @endif
</td>
   <td>
        @if($user->status ==1)
        <span class="status-badge active">
            Active
        </span>
        @else
        <span class="status-badge inactive">
            Inactive
        </span>
        @endif
    </td>
      <td >
                <div class="product-image-box">
<a href="#" class="btn-icon edit">
                                  <i class="ti ti-pencil-minus"></i>
                                </a>
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" 
                                            onclick="return confirm('Delete this category?')">
                                      <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                </div>
                                
                            </td>
</tr>
@endforeach
    </tbody>
</table>


   </div>
   <!-- Pagination -->
<div >
    {{ $users->links() }}
</div>
</div>


@endsection