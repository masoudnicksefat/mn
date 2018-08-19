jQuery(function($) {
    var $el     = $('.gsdFastEdit')
        baseURL = $el.data("base") + "/index.php?option=com_gsd&view=items&format=raw";
        thing   = $el.data("thing")
        plugin  = $el.data("plugin")
        $modal  = $el.find(".modal");

    // Load Items on page load only if we are editing an exiting item
    $(window).load(function() {
        if (thing) {
            get();
        }
    })

    // Open Add/Edit Modal Event
    $(document).on("click", ".btn[href='#gsdModal']", function() {
        isAdd = $(this).hasClass("add");

        // Change Modal Title
        modalHeader = isAdd ? Joomla.JText._('GSD_ADD_SNIPPET') : Joomla.JText._('GSD_EDIT_SNIPPET');
        $modal.find(".modal-header h3").html(modalHeader);

        // Change IFrame URL and make sure it has been fully loaded before updating the snippets list.
        $modal.find('iframe').attr('src', $(this).attr('data-src')).load(function() {
            get();
        });
    })

    // Remove Item Event
    $(document).on("click", ".gsdRemove", function() {
        if (confirm(Joomla.JText._('NR_ARE_YOU_SURE'))) {
            remove($(this).closest("tr").data("pk"));
        }
        return false;
    })

    /**
     *  Load items
     */
    function get() {
        fetch("&filter_plugin=" + plugin + "&filter_thing=" + thing, function(response) {
            $el.find(".items").html(response);
        })
    }

    /**
     *  Remove an item
     */
    function remove(pk) {
        fetch("&do=delete&pk=" + pk, function() {
            get();
        })
    }

    /**
     *  AJAX Request
     *
     *  @param   String      url       The AJAX URL
     *  @param   Function    callback  The success callback function
     *
     *  @return  void
     */
    function fetch(url, callback) {
        $.ajax({ 
            url: baseURL + url,
            cache: false,
            success: function(response) {
                callback(response);
            },
            beforeSend: function() {
                $el.addClass("working");
            },
            complete: function() {
                $el.removeClass("working");
            }
        });
    }
})