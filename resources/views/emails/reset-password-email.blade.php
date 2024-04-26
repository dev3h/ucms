@extends('emails.mail-template')
@section('contentEmail')
    <div>
        <div>Your password reset request has been accepted.</div>
        <br>
        <div>Below is a link to reset your password. Click the link to set a new password：<a href="{{ $url }}">{{ $url }}</a></div>
        <br>
        <div>If this request was not made by you, please let us know immediately.</div>
        <br>
        <div>If you have any questions or problems, please feel free to contact us.</div>
    </div>
@endsection
