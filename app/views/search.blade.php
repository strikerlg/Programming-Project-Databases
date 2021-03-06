@extends('layouts.master')

@section('content')
	<div class="row" style="margin-top:40px">
		{{ Form::open(array('url' => 'search', 'class'=>'form-inline')) }}
		<div class="col-md-11">
			{{ Form::text('input', $input, array('class'=>'form-control', 'style' => 'width:100%;')) }}
		</div>
		<div class="col-md-1">
			{{ Form::submit('Search', array('class'=>'btn btn-info pull-right')) }}
			
			{{ Form::token() . Form::close() }}
		</div>
		<div class="col-md-12 visible-md visible-lg">
			<hr />
		</div>
	</div>
	<div class="row">
		@if (empty($teams) == false)
			<div class="col-md-12">
				<h3>Teams</h3>
			</div>
			@foreach ($teams as $team)
				<div class="col-md-2">
					<a href="{{ route('team', array('id'=>$team->id))}}"><i class='flag-<?php echo $team->abbreviation; ?>'></i> {{ $team->name }}</a>
				</div>
			@endforeach
		@endif

		@if (empty($players) == false)
			<div class="col-md-12">
				<h3>Players</h3>
			</div>
			@foreach ($players as $player)
				<div class="col-md-2">
					<div style="width:100%; height:100px;background-position:cover; background-image:url('<?php echo Player::getPlayerImageURL($player->name); ?>')" ></div>
					<a href="{{ route('player', array('id'=>$player->id))}}"></i> {{ $player->name }}</a>
				</div>
			@endforeach
		@endif
		
		@if (empty($matches) == false)
			<div class="col-md-12">
				<h3>Matches</h3>
			</div>
			@foreach ($matches as $match)
				<div class="col-md-2">
					<a href="{{route('match', array('id'=>$match->id))}}">{{$match->hometeam}} - {{$match->awayteam}}</a>
				</div>
			@endforeach
		@endif
		
		@if (empty($users) == false)
			<div class="col-md-12">
				<h3>Users</h3>
			</div>
			@foreach ($users as $user)
				<div class="col-md-2">
					<a href="{{url('profile').'/'.$user->id}}">{{$user->username}}</a>
				</div>
			@endforeach
		@endif
		
		@if (empty($usergroups) == false)
			<div class="col-md-12">
				<h3>Usergroups</h3>
			</div>
			@foreach ($usergroups as $usergroup)
				<div class="col-md-2">
					@if($usergroup->private != 1)
					<a href="{{url('usergroup').'/'.$usergroup->id}}">{{$usergroup->name}}</a>
					@endif
				</div>
			@endforeach
		@endif
	</div>
@stop
