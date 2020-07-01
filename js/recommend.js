$(document).ready(function () {
    $('.ilc_section_LinkContainer').each(function(index){



        try{

            /**
             * Get the embedded links
             */
            const topic_link=$('#sortByTopic').attr('href');
            const branch_link=$('#sortByBranch').attr('href'); 
            const home_link=$('#backHome').attr('href');


            console.log(home_link);
            
            const href=$(this).attr('href');
            const SORT_BY_BRANCH="cmd=sortBranch";
            const SORT_BY_TOPIC="cmd=sortTopic";
            const BACK_TO_OBJECT="target=";
            
            if (href.includes(SORT_BY_BRANCH)){
                //console.log($(this));

                $(this).html($("#branches").html());
                //$(this).attr("target","_self");
                $(this).attr("class","filter_dropdown");
            }
            if (href.includes(SORT_BY_TOPIC)){
                //console.log($(this));
                $(this).html($("#topics").html());
                $(this).attr("class","filter_dropdown");
            }
            if (href.includes(BACK_TO_OBJECT)){
                //console.log($(this));
                $(this).attr("href",home_link);
                $(this).attr("target","_self");
            }
        }catch(e){

        }
        
    });
    var $modal = $(".tile_recommend_modal");

    $(".tile_icon_recommend").each(function (i, button) {

        var $button = $(button);
        var $form;
        var $submit;

        $button.click(click);

        /**
         * @returns {boolean}
         */
        function click() {
            var get_url = $button.attr("href");

            $.get(get_url, show);

            return false;
        }

        /**
         * @param {string} html
         */
        function show(html) {
            $modal.find(".modal-body").html(html);

            $form = $("#form_tile_recommend_modal_form");
            $submit = $("#tile_recommend_modal_submit");
            var $cancel = $("#tile_recommend_modal_cancel");

            $form.submit(submit);
            $cancel.click(cancel);

            $modal.modal("show");
        }

        /**
         * @returns {boolean}
         */
        function submit() {
            var post_url = $form.attr("action");

            var data = new FormData($form[0]);
            data.append($submit.prop("name"), $submit.val()); // Send submit button with cmd

            $.ajax({
                type: "post",
                url: post_url,
                contentType: false,
                processData: false,
                data: data,
                success: show
            });

            return false;
        }

        /**
         * @returns {boolean}
         */
        function cancel() {
            $modal.modal("hide");

            return false;
        }
    });
});
