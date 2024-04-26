@extends('emails.mail-template')
@section('contentEmail')
    <div>
        <div> A new user administrator has been created from the {{ $admin_name }}。</div>
        <br>
        <div>The following is the information for the new user administrator。</div>
        <br>
        <div>Name： {{ $name }}</div>
        <div>Email： {{ $email }}</div>
        <div>Password: {{ $password }}</div>
        <div>Create At： {{ $created_at }}</div>
        <br>
        <div>To access your account, please click on the link below to log in：<a href="{{ $url }}">{{ $url }}</a></div>
        <br>
        <div>If you have any questions or problems, please contact us at any time。</div>
    </div>
@endsection
