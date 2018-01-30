$(document).ready(function(){
    
    $(".delbtn").click(function(event){
        if(confirm("Are you sure you want to delete your comment?"))
        {
            $.ajax({
                method:'POST',
                url:'delete-comment',
                data:{
                    id:$(this).attr('for'),
                },
                success: function(id){
                    $('.delbtn').each(function(){
                        if($(this).attr('for')==id)
                        {
                            $(this).parent(".item-comment").remove();
                        }
                    })
                }
            })
        }
        
        event.preventDefault();
    })
    
    $('.updbtn').click(function(event){
        var id=$(this).attr('for');
        $('.update_input').val(id);
        $('textarea').val($(this).parent('.item-comment').children('.comment-text').html());
        event.preventDefault();
    })
})