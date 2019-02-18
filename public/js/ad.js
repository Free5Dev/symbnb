 $('#add-image').click(function(){
            //Je recupere le numéro des futurs champs que je vais créer 
            const index = +$('#widgets-counter').val();
            console.log(index);
            // je recupere le prototype des entrées 
            const tmpl=$('#ad_images').data('prototype').replace(/__name__/g, index);
            
            //j'injecte ce code au sein de la div
            $('#ad_images').append(tmpl);

             $('#widgets-counter').val(index + 1);
            // Je gere le button supprime
            handleDeleteButtons();
        });
        function handleDeleteButtons(){
            $('button[data-action="delete"]').click(function(){
                const target=this.dataset.target;
                $(target).remove();
            });
        }
        function updateCounter(){
            const count= +$('#ad_images div.form-group').length;
            $('#widgets-counter').val(count);
        }
        updateCounter();
        handleDeleteButtons();