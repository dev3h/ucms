@extends('emails.mail-template')
@section('contentEmail')
    <div>
        <div>{{__('email.create-account.content.line1')}} {{ $admin_name }}。</div>
        <div>{{__('email.create-account.content.line2')}}</div>
        <br>
        <div>{{__('email.create-account.content.line3')}} {{ $name }}</div>
        <div>{{__('email.create-account.content.line4')}} {{ $email }}</div>
        <div>{{__('email.create-account.content.line5')}} {{ $password }}</div>
        <div>{{__('email.create-account.content.line6')}} {{ $created_at }}</div>
        <br>
        <div>{{__('email.create-account.content.line7')}} <a href="{{ $url }}">{{ $url }}</a></div>
        <br>
        <div>{{__('email.create-account.content.line8')}}</div>
    </div>
@endsection
