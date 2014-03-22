/**
 * Created by Pieterjan Lambrecht on 18/03/14.
 */

function addVote(){
    var vote_counter = $.cookie("voted_nb");
    $("#vote_counter").html(vote_counter);
}

$(document).ready(function(){

    // Music search
    $("#music_search").click(function(){
        var search_term = $("#music_search_field").val();
        if(!(!search_term || search_term.length < 2)){
            $("#music_search_results").show("slow");
            $("#music_search_spacer_2").show("slow");
            $.ajax({
                type: "POST",
                url: "api.php",
                dataType: "json",
                data : {"request" : "music_search", "option" : search_term},
                success: function(json_data){
                    if(json_data.length > 0){
                        $("#music_search_results_body").html("");
                        $.each(json_data, function(i, v){
                            $("#music_search_results_body").append('<li class="list-group-item">'+v[1]+' - '+v[2]+'<span id="'+v[0]+'" class="space-right music_new_vote pull-right">Vote! <span class="glyphicon glyphicon-thumbs-up"></span></span></li>\n');
                        });
                    } else {
                        $("#music_search_results_body").html("Nothing found matching your search query!");
                    }
                },
                error: function(){
                    alert("An error happened while searching for music!");
                }
            })
        }
    });

    // Close notification
    $("#close_notif").click(function(){
        $.cookie("closed_notif", 1, { expires: 7 });
    });

    // Vote on existing votes
    $(".music_vote").click(function(){
        var vote_badge = $(this).parent().find('.badge');
        var vote_id = this.id;
        var post_data = {"request" : "vote_submit", "option" : vote_id};
        $.ajax({
            type: "POST",
            url: "api.php",
            data: post_data,
            success: function(){
                // Update vote counters
                addVote();
                var total_votes = parseInt(vote_badge.text());
                $(vote_badge).html(total_votes+1);
            },
            error: function(){
                // Couldn't submit the vote
                alert("An error happened while submitting your vote!");
            }
        })
    });

    // Vote on new (?) votes
    $(document).on("click", ".music_new_vote", function(ev){
        var this_element = this;
        var vote_button = $(this).find('.glyphicon');
        var vote_id = this.id;
        var post_data = {"request" : "new_vote_submit", "option" : this.id};
        $.ajax({
            type: "POST",
            url: "api.php",
            data: post_data,
            success: function(){
                // Update vote counters & disable vote buttons
                addVote();
                // Cannot vote anymore
                $(this_element).removeClass("music_new_vote");
                $(vote_button).removeClass("glyphicon-thumbs-up");
                $(vote_button).addClass("glyphicon-ok");
            },
            error: function(){
                // Couldn't submit the vote
                alert("An error happened while submitting your vote!");
            }
        })
    });
});