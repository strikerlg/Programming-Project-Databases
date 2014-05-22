@extends('layouts.master')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Upcoming matches</h1>
      <?php
        $user = new User;
        if($user->loggedIn()){
          // Logged in user.
      ?>
      <p>Grey rows mean that you've already bet on them.</p>
      <?php
        }
      ?>
    </div>
    <div class="col-md-12">
      Search: <input name="filter" id="filter-box" value="" maxlength="30" size="30" type="text" placeholder="Date or Name">
      <input id="filter-clear-button" type="submit" value="Clear"/>
      <table id="myTable" class="tablesorter">
        <thead class="center">
          <tr>
            <th>Date</th>
            <th>Home</th>
            <th>vs.</th>
            <th>Away</th>
            <th>Check match</th>
          </tr>
        </thead>
        <tbody>
              <?php
                $user = new User;
                if($user->loggedIn()){
                  // Logged in user.
              ?>
              @foreach($matches as $match)
                <?php
                  if ($match->bet) { ?>
                  <tr class="bet">
                <?php } else { ?>
                  <tr class="regular">
                <?php } ?>
                    <td><?php $date = new DateTime($match->date);
								  echo $date->format('d-m-Y H:i');
						?>
					</td>
                    <td><a href="{{route('team', array('id'=>$match->hometeam_id))}}">{{$match->hometeam}}</a></td>
                    <td> - </td>
                    <td><a href="{{route('team', array('id'=>$match->awayteam_id))}}">{{$match->awayteam}}</a></td>
                    <td><a href="{{route('match', array('id'=>$match->id))}}"}}><button type="button" class="btn btn-default btn-sm">Go!</button></a></td>
                  </tr>
              @endforeach
              <?php
            } else {
              // Not logged in.
              ?>
              @foreach($matches as $match)
                  <tr class="mark">
                    <td>{{$match->date}}</td>
                    <td><a href="{{route('team', array('id'=>$match->hometeam_id))}}">{{$match->hometeam}}</a></td>
                    <td> - </td>
                    <td><a href="{{route('team', array('id'=>$match->awayteam_id))}}">{{$match->awayteam}}</a></td>
                    <td><a href="{{route('match', array('id'=>$match->id))}}"}}><button type="button" class="btn btn-default btn-sm">Go!</button></a></td>
                  </tr>
              @endforeach
              <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
@stop

@section('css')
<style>
  .center{
    text-align:center;
  }

  .center th {
    text-align:center;
  }

  .bet {
    background-color:#EDEDED;
  }
</style>
@stop

@section('javascript')
<script src="<?php echo asset('js/jquery-1-3-2.js'); ?>" ></script>
  <script src="<?php echo asset('js/tablesorter.js'); ?>" ></script>
  <script src="<?php echo asset('js/tablesorter_filter.js'); ?>" ></script>

  <script type="text/javascript">
    jQuery(document).ready(function() {
        $("#myTable")
        .tablesorter({debug: false, widgets: ['zebra'], sortList: [[0, 0]], headers: {2: {sorter: false}, 4: {sorter: false}}})
        .tablesorterFilter({filterContainer: "#filter-box",
                            filterClearContainer: "#filter-clear-button",
                            filterColumns: [0, 1, 3]}); });
  </script>
@stop
