@extends('emails.mail-template')
@section('contentEmail')
    <div>
        <div>{{__('email.reset-password.content.line1')}}</div>
        <br>
        <div>{{__('email.reset-password.content.line2')}}</div>
        <br>
        <div>{{__('email.reset-password.content.line3')}}</div>
        <br>
        <div>{{__('email.reset-password.content.line4')}} <a href="{{ $url }}">{{ $url }}</a></div>
        <br>
        <div>{{__('email.reset-password.content.line5')}}</div>
        <br>
        <div>{{__('email.reset-password.content.line6')}}</div>
    </div>
@endsection
