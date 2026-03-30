<?php
use Illuminate\Support\Facades\Session;

$userid = Session::get('user')['name']; 

?>


@extends('master')
@section('content')


<div class="content-body">

@if($userid == "razzaq")
<iframe title="UserLog" width="100%" height="500.5" src="https://app.powerbi.com/view?r=eyJrIjoiOTBiNTMzMmItODZjMC00YTU1LWEzNjktY2M3MjgwMzgyMjEyIiwidCI6IjA3MmQ1YWJhLTEyMzAtNGFjNy04NGMzLTFjMzkxOTliMjMyNSIsImMiOjl9" frameborder="0" allowFullScreen="true"></iframe>
@endif    
</div>



@endsection