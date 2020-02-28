var FormValidationDestekDatabase = function () {

    var handleValidation = function() {
        $('#tree_1').jstree({
            "core" : {
                "themes" : {
                    //"responsive": true
                    "variant" : "large"
                }
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "kategori" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "urun" : {
                    "icon" : "fa fa-tag icon-state-info icon-lg"
                },
                "parca" : {
                    "icon" : "fa fa-cog icon-state-info icon-lg"
                }

            },
            "plugins": ["types"]
        });

        // handle link clicks in tree nodes(support target="_blank" as well)
        $('#tree_1').on('select_node.jstree', function(e,data) {
            var link = $('#' + data.selected).find('a');
            if (link.attr("href") != "#" && link.attr("href") != "javascript:;" && link.attr("href") != "") {
                if (link.attr("target") == "_blank") {
                    link.attr("href").target = "_blank";
                }
                document.location.href = link.attr("href");
                return false;
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {

            handleValidation();
        }

    };

}();