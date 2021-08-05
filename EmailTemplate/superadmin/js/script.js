$(document).ready(function() {

	$(".custom-file-input").on("change", function(){		
		readURL(this);
	});
	
	function readURL(input) {
		if (input.files && input.files[0]) {
			var file = input.files[0].name;
				
			var split = file.split('.');
			var filename = split[0];
			var extension = split[1];	
			if (filename.length > 10) {
				filename = filename.substring(0, 10);
			}
						
			file = filename + '.' + extension;
				
			$(input).next(".custom-file-control").addClass('selected');
			$(input).next(".custom-file-control").attr('data-after',file); 
		}
	}
	$("#filefield").on('change', function(){
		
	});
	
	
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        zIndex: 4048,
    });

    $(".users-table_old").tablesorter({
        headers: {
            2 : {
                sorter: false
            },
            3 : {
                sorter: false
            },
            4 : {
                sorter: false
            },
            5 : {
                sorter: false
            },
            6 : {
                sorter: false
            },
            7 : {
                sorter: false
            },
            8 : {
                sorter: false
            },
            9 : {
                sorter: false
            }
        }
    });

    $(".transactions-table_old").tablesorter({
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

    $(".products-table_old").tablesorter({
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

    $(".orders-table_old").tablesorter({
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