$(document).ready(function() {
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        zIndex: 4048,
    });

    $(".users-table").tablesorter({
        headers: {
            2: {
                sorter: false
            },
            5: {
                sorter: false
            },
            6: {
                sorter: false
            }
        }
    });

    $(".transactions-table").tablesorter({
        headers: {
            3: {
                sorter: false
            },
            4: {
                sorter: false
            },
            5: {
                sorter: false
            },
            6: {
                sorter: false
            },
            dateFormat: "us"
        }
    });

    $(".products-table").tablesorter({
        headers: {
            1: {
                sorter: false
            },
            5: {
                sorter: false
            },
            6: {
                sorter: false
            },
            7: {
                sorter: false
            }
        }
    });

    $(".orders-table").tablesorter({
        headers: {
            3: {
                sorter: false
            },
            4: {
                sorter: false
            },
            5: {
                sorter: false
            },
            6: {
                sorter: false
            }
        }
    });
});