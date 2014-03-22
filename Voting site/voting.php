<?php
/**
 * Created by PhpStorm.
 * User: Pieterjan Lambrecht
 * Date: 10/03/14
 * Time: 16:22
 */

class Voting {
    // API credentials
    private $user = "raspberry_pi_fm";
    private $password = "some_password";
    // Credentials for database
    private $dbhost = "localhost";
    private $dbuser = "root";
    private $songdbname = "pisongs";
    private $dbpw = "another_password";
    // Database connection
    private $connection = NULL;
    // Are you logged in yet or what?
    private $loggedin = false;

    /**
     * Destructor; for cleanup purposes
     */
    function __destruct(){
        if($this->connection){
            $this->connection->close();
        }
    }

    /**
     * Connect to the song database
     * @return bool Returns if successful or not
     */
    function connectSong(){
        if(!$this->connection){
            $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpw, $this->songdbname);
            if ($this->connection->connect_error) {
                return false;
            }
        }
        return true;
    }

    /**
     * Inserts a song into the database
     * @param $artist string Artist of the song
     * @param $title string Title of the song
     * @return string Some feedback of the insertion process
     */
    function insertSong($artist, $title){
        if($this->loggedin && $this->connectSong()){
            $stmt = mysqli_stmt_init($this->connection);
            if(mysqli_stmt_prepare($stmt, "INSERT INTO pisongs(song_artist, song_title) VALUES(?, ?)")){
                mysqli_stmt_bind_param($stmt, "ss", $artist, $title);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                return "Song inserted!";
            }
        }
        return "Error happened while inserting song!";
    }

    /**
     * Deletes a song from the database
     * @param $artist string Artist of the song
     * @param $title string Title of the song
     * @return string Gives us some feedback about the delete process
     */
    function deleteSong($artist, $title){
        if($this->loggedin && $this->connectSong()){
            $stmt = mysqli_stmt_init($this->connection);
            if(mysqli_stmt_prepare($stmt, "DELETE FROM pisongs WHERE song_artist = ? AND song_title = ?")){
                mysqli_stmt_bind_param($stmt, "ss", $artist, $title);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                return "Song deleted!";
            }
        }
        return "Error happened while deleting song!";
    }

    /**
     * @param $votegid
     */
    function submitVote($votegid){
        if($this->canVote() && $this->connectSong()){
            // Database part
            if(mysqli_query($this->connection, "UPDATE pivotes SET pivotes.vote_amount = pivotes.vote_amount + 1 WHERE pivotes.vote_id = ".$votegid)){
                // Cookie part
                $this->addVote();
                return;
            }
        }
        // Unsuccesful request
        header('HTTP/1.1 500');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => "Error submitting vote")));
    }

    function addVote(){
        if($_COOKIE['voted_nb'] < 1){
            setcookie("voted_nb", 1, time() + 120);
            setcookie("voted_first", time(), time() + 120);
        } elseif($_COOKIE['voted_nb'] < 5){
            setcookie("voted_nb", $_COOKIE['voted_nb'] + 1, time() + 120);
        } else {
            setcookie("voted_first", time(), time() + 120);
            setcookie("voted_nb", 0, time() + 120);
        }
    }

    /**
     * @return bool
     */
    function canVote(){
        if($_COOKIE['voted_nb'] < 5){
            return true;
        }
        elseif($_COOKIE['voted_nb'] == 5 && ($_COOKIE['voted_first'] + 120) < time()){
            return true;
        }
        elseif(!isset($_COOKIE['voted_nb']) || !isset($_COOKIE['voted_first'])){
            setcookie("voted_first", time(), time()+120);
            setcookie("voted_nb", 0, time()+120);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Pops the top voted song and returns it information as JSON
     * @return string JSON encoded array with top-voted information
     */
    function popVote(){
        if($this->loggedin && $this->connectSong()){
            if($res = mysqli_query($this->connection, "SELECT pisongs.song_id, pisongs.song_artist, pisongs.song_title, pivotes.vote_amount FROM pivotes INNER JOIN pisongs ON pisongs.song_id = pivotes.song_id ORDER BY pivotes.vote_amount DESC LIMIT 1")){
                $popped = mysqli_fetch_array($res, MYSQLI_NUM);
                mysqli_free_result($res);
                if(mysqli_query($this->connection, "DELETE FROM pivotes WHERE song_id = ".$popped[0]) && $popped){
                    return json_encode($popped);
                }
            }
        }
        return "Error happened while popping the vote!".mysqli_error($this->connection);
    }

    /**
     * List all the current votes in a JSON format
     * @return string Information about all the current submitted votes
     */
    function listVotes(){
        if($this->loggedin && $this->connectSong()){
            if($res = mysqli_query($this->connection, "SELECT pisongs.song_artist, pisongs.song_title, pivotes.vote_amount FROM pivotes INNER JOIN pisongs ON pisongs.song_id = pivotes.song_id ORDER BY pivotes.vote_amount DESC")){
                $json = array();
                $i = 0;
                while($row = mysqli_fetch_array($res, MYSQLI_NUM)){
                    $json[$i] = $row;
                    $i++;
                }
                mysqli_free_result($res);
                return json_encode($json);
            }
        }
        return "Error happened while listing votes!";
    }

    /**
     * @return string
     */
    // TODO: What if there are no votes?
    function printVotes(){
        if($this->connectSong()){
            $result = "";
            if($res = mysqli_query($this->connection, "SELECT pisongs.song_artist, pisongs.song_title, pivotes.vote_id, pivotes.vote_amount FROM pivotes INNER JOIN pisongs on pisongs.song_id = pivotes.song_id ORDER BY pivotes.vote_amount DESC")){
                if(mysqli_num_rows($res) < 1){
                    mysqli_free_result($res);
                    return "No votes found!";
                }
                $result .= "<ul class=\"list-group\">";
                while($row = mysqli_fetch_array($res, MYSQLI_NUM)){
                    $result .= "<li class=\"list-group-item\">".$row[0]." - ".$row[1]."<span class=\"badge\">".$row[3]."</span><span id=\"".$row[2]."\" class=\"space-right music_vote pull-right\">Vote! <span class=\"glyphicon glyphicon-thumbs-up\"></span></span></li>\n";
                }
                $result .= "</ul>";
            }
            mysqli_free_result($res);
            return $result;
        }
        return "Error happened while printing votes!";

    }

    /**
     * Check those credentials!
     * @param $user string Provided username
     * @param $pass string Some password
     * @return bool Returns if is correctly logged in or not
     */
    function verifyLogin($user, $pass){
        if(!$this->loggedin){
            if($this->user == $user && $this->password == $pass){
                $this->loggedin = true;
            } else {
                $this->loggedin = false;
            }
        }
        return $this->loggedin;
    }

    /**
     * @return int
     */
    function printVoteCount(){
        if(!isset($_COOKIE['voted_nb']) || !isset($_COOKIE['voted_first'])){
            setcookie("voted_first", time(), time()+120);
            setcookie("voted_nb", 0, time()+120);
            return 0;
        } else {
            return $_COOKIE['voted_nb'];
        }
    }

    /**
     * @param $search_string
     */
    function searchSong($search_string){
        if($this->connectSong()){
            $search_terms = array_filter(preg_split("/[\s,\-\t ]/", $search_string), function($s){ return !($s == NULL || strtoupper($s) == "THE" || strtoupper($s) == "A" || strtoupper($s) == "AN"); });
            $results = array();
            $i = 0;
            $stmt = mysqli_stmt_init($this->connection);
            if(mysqli_stmt_prepare($stmt, "SELECT song_id, song_artist, song_title FROM pisongs WHERE song_artist LIKE ? OR song_title LIKE ?")){
                foreach($search_terms as $term){
                    $liketerm = "%".$term."%";
                    mysqli_stmt_bind_param($stmt, "ss", $liketerm, $liketerm);
                    mysqli_stmt_execute($stmt);
                    $res = mysqli_stmt_get_result($stmt);
                    while($row = mysqli_fetch_array($res, MYSQLI_NUM)){
                        $results[$i] = $row;
                        $i++;
                    }
                    mysqli_free_result($res);
                }
            }
            mysqli_stmt_close($stmt);
            header('Content-Type: application/json');
            die(json_encode($results));
        }
        header('HTTP/1.1 500');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => "Error searching vote")));
    }

    /**
     * @param $songid
     * TODO: Check if song is already voted
     */
    function submitNewVote($songid){
        if($this->connectSong() && $this->canVote()){
            $stmt = mysqli_stmt_init($this->connection);
            if(mysqli_stmt_prepare($stmt, "INSERT INTO pivotes(song_id) VALUES(?)")){
                mysqli_stmt_bind_param($stmt, "i", $songid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $this->addVote();
                die(json_encode(array('message' => 'SUCCESS', 'code' => "New vote submitted!")));
            }
        }
        header('HTTP/1.1 500');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => "Error submitting vote")));
    }
}

?>