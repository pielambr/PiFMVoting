<?php
require('voting.php');
$voting = new Voting();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PiFM Radio - Voting</title>
    <!-- jQuery -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.0/jquery.cookie.min.js"></script>
    <!-- Bootstrap imports -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- Some extra JavaScript code -->
    <script src="js/pifm.js"></script>
</head>
<body>
<div class="container">

    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Site name and button for collapsing menu -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><strong>PiFM Radio</strong> <span class="glyphicon glyphicon-music"></span></a>
            </div>
            <!-- Collapsable menu-items -->
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">About</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if(!isset($_COOKIE['closed_notif'])){ ?>
    <!-- An alert about voting -->
    <div class="alert alert-info alert-dismissable">
        <button id="close_notif" type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Reminder:</strong> you can only vote <strong>five</strong> times every <strong>two</strong> minutes!
    </div>
    <?php } ?>

    <!-- Search thingy -->
    <div class="col-lg-8 col-lg-offset-2">
        <div class="input-group">
            <input type="text" class="form-control" id="music_search_field" placeholder="Search for a song: artist - title">
            <span class="input-group-btn">
                <button class="btn btn-default" id="music_search" type="button">Search! <span class="glyphicon glyphicon-search"></span></button>
            </span>
        </div>
    </div>

    <!-- Spacer -->
    <div class="col-lg-8 col-lg-offset-2 invisible" id="music_search_spacer">
        <div class="panel-body">
            <span></span>
        </div>
    </div>

    <!-- Search results -->
    <div class="col-lg-8 col-lg-offset-2 hidden_results" id="music_search_results">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Search results</h3>
            </div>
            <div class="panel-body" id="music_search_results_body">
                <img src="img/ajax-loader.gif" alt="loading" class="center-block img-responsive" />
            </div>
        </div>
    </div>

    <!-- Spacer -->
    <div class="col-lg-8 col-lg-offset-2 invisible" id="music_search_spacer_2">
        <div class="panel-body">
            <span></span>
        </div>
    </div>

    <!-- Overview of the votes -->
    <div class="col-lg-8 col-lg-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Current votes - You have voted <span id="vote_counter"><?php echo $voting->printVoteCount(); ?></span>/5 times</h3>
            </div>
            <div class="panel-body">
                <?php echo $voting->printVotes(); ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>