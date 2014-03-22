<?php
/**
 * Created by PhpStorm.
 * User: Pieterjan Lambrecht
 * Date: 10/03/14
 * Time: 16:10
 */

require('voting.php');

// Stuff that needs authing
if(isset($_POST['user']) && isset($_POST['password'])){
    $votesystem = new Voting();
    if($votesystem->verifyLogin($_POST['user'], $_POST['password'])){
        $req = $_POST['request'];
        $opt = $_POST['option'];
        // API options for song handling
        if($req == "songs"){
            $artist = $_POST['artist'];
            $title = $_POST['title'];
            if($opt == "insert"){
                echo $votesystem->insertSong($artist, $title);
            }
            if($opt == "delete"){
                echo $votesystem->deleteSong($artist, $title);
            }
        }
        // API options for vote handling
        if($req == "votes"){
            if($opt == "list"){
                echo $votesystem->listVotes();
            }
            if($opt == "pop"){
                echo $votesystem->popVote();
            }
        }
    } else {
        die('That was not the right password mate!');
    }
}

// Stuff that doesn't really need authing
if(isset($_POST['request']) && isset($_POST['option'])){
    $req = $_POST['request'];
    $opt = $_POST['option'];
    $voting = new Voting();
    if($req == "new_vote_submit"){
        $voting->submitNewVote($opt);
    } elseif($req == "vote_submit"){
        $voting->submitVote($opt);
    } elseif($req == "music_search"){
        $voting->searchSong($opt);
    }
}

?>