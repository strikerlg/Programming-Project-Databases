@extends('layouts.master')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><a href="{{ route('home') }}">Home</a></li>
				<li class="active">Users</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h1>Users</h1>
		</div>
		<div class="col-md-12">
			<table id="myTable" class="tablesorter ">
				<thead>
					<tr>
						<th>Username</th>
						<th>Bet score</th>
					</tr>
				</thead>
				<tbody>	
					@foreach ($users as $user)
						<tr>
							<td><a href="{{url('profile/'.$user->id)}}">{{$user->username}}</a></td>
							<td>{{$user->betscore}}</td>
						</tr>
					@endforeach					
				</tbody>
			</table>
		</div>
	</div>
@stop
<link rel="stylesheet" href="{{asset('css/tablesorter.bootstrap.css')}}">
@section('css')

@stop


@section('javascript')
	<script src="<?php echo asset('js/tablesorter.js'); ?>" ></script>
	<script src="<?php echo asset('js/tablesorter_filter.js'); ?>" ></script>

	<script type="text/javascript">
		jQuery(document).ready(function() {
		
        $("#myTable")
        .tablesorter({debug: false, widgets: ['zebra'], sortList: [[2,0], [1, 0]]})
        .tablesorterFilter({filterContainer: "#filter-box",
                            filterClearContainer: "#filter-clear-button",
                            filterColumns: [0]}); });
	</script>
@stop
