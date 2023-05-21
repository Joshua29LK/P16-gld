var config = {
    map: {
        '*': {
            jquerypopper: "Sm_Market/js/bootstrap/popper",
            jquerybootstrap: "Sm_Market/js/bootstrap/bootstrap.min",
            owlcarousel: "Sm_Market/js/owl.carousel",
            jqueryfancyboxpack: "Sm_Market/js/jquery.fancybox.pack",
            jqueryunveil: "Sm_Market/js/jquery.unveil",
            yttheme: "Sm_Market/js/yttheme"
        }
    },
    shim: {
        'jquerypopper': {
            'deps': ['jquery'],
            'exports': 'Popper'
        },
        'jquerybootstrap': {
            'deps': ['jquery', 'jquerypopper']
        }
    }
};