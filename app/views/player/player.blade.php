@extends('layouts.master')


@section('content')
<?php
	function cardColorToImg($color){
		if($color == 'red'){
			return "<img src='". asset('img/redcard.png') ."' style='height:16px;' />";
		}else if($color == 'yellow'){
			return "<img src='". asset('img/yellowcard.png') ."' style='height:16px;' />";
		}
	}
?>

<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="{{ route('home') }}">Home</a></li>
		    <li><a href="{{ route('team', array('id'=>$playerTeam->id)) }}">{{$playerTeam->name}}</a></p>
			<li class="active">{{$playerObj->name}}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-md-2">
		<img class="img-responsive flag" src="{{$playerImageURL}}" alt="" />
		<h2>{{$playerObj->name}}</h2>
		<p><b>Team: </b> <a href="{{ route('team', array('id'=>$playerTeam->id)) }}">{{$playerTeam->name}}</a></p>
		<p><b>Goals: </b> <?php echo Player::countGoals($playerObj->id); ?></p>
	</div>
	<div class="col-md-10">
		<h3>Information</h3>
		<p>{{$playerText}}</p>
		<h3>Goals</h3>
		<table id="myTable" class="tablesorter">
			<thead>
				<tr>
					<th>Date</th>
					<th>Time</th>
					<th>Match</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($goals as $goal)
					<tr>
						<td><?php $date = new DateTime($goal->date);
								  echo date_format($date, 'd-m-Y H:i');
						?></td>
						<td><?php echo $goal->time; ?></td>
						<td><a href="<?php echo route('match', array('id'=>$goal->match_id)); ?>">{{$goal->hometeam}} - {{$goal->awayteam}} </a></td>
					<tr>
				@endforeach
			</tbody>
		</table>
		<h3>Cards</h3>
		<table id="myTable2" class="tablesorter">
			<thead>
				<tr>
					<th>Date</th>
					<th>Time</th>
					<th>Match</th>
					<th>Color</th>
				</tr>
			</thead>
			<tbody>	
				@foreach ($cards as $card)
					<tr>
						<td><?php $date = new DateTime($card->date);
								  echo date_format($date, 'd-m-Y H:i');
						?></td>
						<td><?php echo $card->time; ?></td>
						<td><a href="<?php echo route('match', array('id'=>$card->match_id)); ?>">{{$card->hometeam}} - {{$card->awayteam}} </a></td>
						<td><?php echo cardColorToImg($card->color);?></td>
					<tr>
				@endforeach
			</tbody>
		</table>
		<h3>Matches</h3>
		<table id="myTable3" class="tablesorter">
			<thead>
				<tr>
					<th>Date</th>
					<th>Match</th>
					<th>Score</th>
				</tr>
			</thead>
			<tbody>	
				@foreach ($matches as $match)
					<tr>
						<td><?php if($match->date == "0000-00-00 00:00:00") 
							echo "date unknown"; 
						  else {
							$date = new DateTime($match->date);
  						    echo date_format($date, 'd-m-Y H:i');
						  }
						?></td>
						<td><a href="{{route('match', array('id'=>$match->id))}}">{{ $match->hometeam}} - {{ $match->awayteam }} </a></td>
						<td><?php if (Match::isPlayed($match->id)) echo Match::getScore($match->id); else echo "? - ?" ?></td>
					<tr>
				@endforeach
			</tbody>
		</table>

    </div>
</div>
@stop

@section('javascript')
  <script src="<?php echo asset('js/tablesorter.js'); ?>" ></script>
  <script src="<?php echo asset('js/tablesorter_filter.js'); ?>" ></script>

  <script type="text/javascript">
    jQuery(document).ready(function() {
        $("#myTable3")
        .tablesorter({debug: false, dateFormat: "uk", widgets: ['zebra'], sortList: [[0, 1]]})
        .tablesorterFilter({filterContainer: "#filter-box",
                            filterClearContainer: "#filter-clear-button",
                            filterColumns: [0, 1, 2]}); });
  </script>
  <script type="text/javascript">
    jQuery(document).ready(function() {
        $("#myTable2")
        .tablesorter({debug: false, widgets: ['zebra'], sortList: [[0, 1]]})
        .tablesorterFilter({filterContainer: "#filter-box",
                            filterClearContainer: "#filter-clear-button",
                            filterColumns: [0, 1, 2]}); });
  </script>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        $("#myTable")
        .tablesorter({debug: false, widgets: ['zebra'], sortList: [[0, 1]]})
        .tablesorterFilter({filterContainer: "#filter-box",
                            filterClearContainer: "#filter-clear-button",
                            filterColumns: [0, 1, 2]}); });
  </script>
@stop

