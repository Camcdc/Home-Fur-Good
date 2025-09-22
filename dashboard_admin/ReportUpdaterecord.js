function selectInspector(userID, inspectorName) {
                            $('#userID').val(userID);
                            $('#popup').hide(300);
                            $('#overlay').hide();
                            $('#selectedInspector').text('Selected: ' + inspectorName);
                        }
                        $(document).ready(function(){
                            // Hide popup and overlay initially
                            $('#popup').hide();
                            $('#overlay').hide();
                            // Show selected inspector if already set
                            var initialUserID = $('#userID').val();
                            if(initialUserID) {
                                // Optionally fetch inspector name via AJAX or PHP if needed
                                $('#selectedInspector').text('Selected Inspector ID: ' + initialUserID);
                            }
                            $('.trigger_popup_fricc').click(function(e){
                                e.preventDefault();
                                $('#popup').show(300);
                                $('#overlay').show();
                            });
                            $('.popup_close, #overlay').click(function(){
                                $('#popup').hide(300);
                                $('#overlay').hide();
                            });
                        });