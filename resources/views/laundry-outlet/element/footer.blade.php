<!-- Footer -->
<?php $loggedin_user = Auth::user('laundry_outlet');
$merchant = $loggedin_user->Merchant;
$merchant_id = $merchant->id;
$laundry_outlet_id = $loggedin_user->id;
?>

<!-- Footer -->
<footer class="site-footer">
    <div class="site-footer-legal">© {{ date('Y').' footer.blade.php'.Auth::user()->Merchant->BusinessName}}</div>
    <div class="site-footer-right"></div>
</footer>
<!-- Core  -->
<script src="{{ asset('global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ asset('global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('global/vendor/intro-js/intro.js') }}"></script>
<script src="{{ asset('global/vendor/screenfull/screenfull.js') }}"></script>
<script src="{{ asset('global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<script src="{{ asset('global/vendor/chartist/chartist.min.js') }}"></script>
<script src="{{ asset('global/vendor/aspieprogress/jquery-asPieProgress.js') }}"></script>
<script src="{{ asset('global/vendor/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('global/vendor/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('global/vendor/bootbox/bootbox.js') }}"></script>
<script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('global/vendor/multi-select/jquery.multi-select.js') }}"></script>

<!-- Scripts -->
<script src="{{ asset('global/js/Component.js') }}"></script>
<script src="{{ asset('global/js/Plugin.js') }}"></script>
<script src="{{ asset('global/js/Base.js') }}"></script>
<script src="{{ asset('global/js/Config.js') }}"></script>
{{--<script>Config.set('theme', '{{ asset('theme') }}');</script>--}}

<script src="{{ asset('theme/js/Section/Menubar.js' ) }}"></script>
<script src="{{ asset('theme/js/Section/GridMenu.js' ) }}"></script>
<script src="{{ asset('theme/js/Section/Sidebar.js' ) }}"></script>
<script src="{{ asset('theme/js/Section/PageAside.js' ) }}"></script>
<script src="{{ asset('theme/js/Plugin/menu.js' ) }}"></script>

<script src="{{ asset('global/js/config/colors.js') }}"></script>
{{--<script src="{{ asset('theme/js/config/tour.js') }}"></script>--}}
<!-- Page -->
<script src="{{ asset('theme/js/Site.js') }}"></script>
<script src="{{ asset('global/js/Plugin/asscrollable.js') }}"></script>
<script src="{{ asset('global/js/Plugin/slidepanel.js') }}"></script>
<script src="{{ asset('global/js/Plugin/switchery.js') }}"></script>
<script src="{{ asset('global/vendor/asspinner/jquery-asSpinner.js') }}"></script>
<script src="{{ asset('global/vendor/asspinner/jquery-asSpinner.min.js') }}"></script>
<script src="{{ asset('global/js/Plugin/aspieprogress.js') }}"></script>
<script src="{{ asset('global/js/Plugin/bootstrap-touchspin.js')}}"></script>
<script src="{{ asset('global/js/Plugin/jquery-placeholder.js')}}"></script>
{{--<script src="{{ asset('theme/examples/js/tables/datatable.js') }}"></script>--}}
<script src="{{ asset('global/js/Plugin/datatables.js') }}"></script>
<script src="{{ asset('global/js/Plugin/select2.js')}}"></script>
<script src="{{ asset('global/js/Plugin/icheck.js')}}"></script>
<script src="{{ asset('global/js/Plugin/bootstrap-select.js')}}"></script>
<script src="{{ asset('global/js/Plugin/ascolorpicker.js')}}"></script>
<script src="{{ asset('global/js/Plugin/multi-select.js')}}"></script>
{{--<script src="{{ asset('global/js/Plugin/bootstrap-datepicker.js') }}"></script>--}}
<script src="{{ asset('global/vendor/typeahead-js/bloodhound.min.js') }}"></script>
<script src="{{ asset('global/vendor/typeahead-js/typeahead.jquery.min.js') }}"></script>
<script src="{{ asset('global/vendor/summernote/summernote.min.js') }}"></script>


<!-- For Taxi Purpose -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js" integrity="sha384-{{generateIntegrityHash('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js')}}" crossorigin="anonymous"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"
        integrity="sha384-bbo3DVXQrozPmDWhLcbqcy0sVdM6FKL9ZpehWvi1ZkhwEx/F7pd0ytCvJjJOlNYY"
        crossorigin="anonymous"></script>

<script src="{{ asset('global/vendor/clockpicker/bootstrap-clockpicker.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
{{--<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js" integrity="sha384-{{generateIntegrityHash('https://unpkg.com/sweetalert/dist/sweetalert.min.js')}}" crossorigin="anonymous"></script>--}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"
        integrity="sha384-RIQuldGV8mnjGdob13cay/K1AJa+LR7VKHqSXrrB5DPGryn4pMUXRLh92Ev8KlGF"
        crossorigin="anonymous"></script>

<script src="{{asset('global/vendor/timepicker/jquery.timepicker.min.js')}}"></script>
<script src="{{asset('upgrade-files/js/jquery.validate-1.19.5.min.js')}}"></script>
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>


<script src="{{ asset('js/my-script.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('.customDatePicker').datepicker({
            format: 'yyyy-mm-dd',
            onRender: function (date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        });
        var dateToday = new Date();
        $('.customDatePicker1').datepicker({
            format: 'yyyy-mm-dd',
            startDate: dateToday,
            onRender: function (date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        });
    });
    $(document).ready(function() {
        $('#customDataTable').DataTable( {
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [-1]
            }],
            //"iDisplayLength": 10,
            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            //"sDom": '<"dt-panelmenu clearfix"Bfr>t<"dt-panelfooter clearfix"ip>',
            //"buttons": ['copy', 'excel', 'csv', 'pdf', 'print'],
            "scrollX": true,
            "paging":   false,
        });
    });
    $(document).ready(function() {
        $('#customDataTable2').DataTable( {
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [-1]
            }],
            //"iDisplayLength": 10,
            "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            //"sDom": '<"dt-panelmenu clearfix"Bfr>t<"dt-panelfooter clearfix"ip>',
            //"buttons": ['copy', 'excel', 'csv', 'pdf', 'print'],
            "scrollX": true,
            "paging":   false,
        });
    });
    (function(document, window, $){
        'use strict';
        var Site = window.Site;
        $(document).ready(function(){
            Site.run();
        });
    })(document, window, jQuery);

    // web notification to display icon on panel
    @if(isset($loggedin_user->OneSignal) && !is_null($loggedin_user->OneSignal->application_key))
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(function (OneSignal) {
            OneSignal.init({
                appId: "{{$loggedin_user->OneSignal->application_key }}",
            welcomeNotification: {
                title: "{{$loggedin_user->full_name}}",
            },
            notifyButton: {
                enable: true,
                colors: { // Customize the colors of the main button and dialog popup button
                    'circle.background': 'rgb(21, 136, 193)',
                    'circle.foreground': 'white',
                    'badge.background': 'rgb(21, 136, 193)',
                    'badge.foreground': 'white',
                    'badge.bordercolor': 'white',
                    'pulse.color': 'white',
                    'dialog.button.background.hovering': 'rgb(77, 101, 113)',
                    'dialog.button.background.active': 'rgb(70, 92, 103)',
                    'dialog.button.background': 'rgb(84,110,123)',
                    'dialog.button.foreground': 'white'
                },
            },
        });

    });
    function foregroundWillDisplayListener(event) {
      var mySound = soundManager.createSound('mySound', '{{asset('sound/notify.mp3')}}');
            mySound.muted = true;
            mySound.play();
            console.log(event);
        @if($merchant->Configuration->sweet_alert_admin == 1)
            swal({
                title: event.heading,
                text: event.content,
                icon: "success",
                buttons: ["@lang("$string_file.skip")", "@lang("$string_file.okay")"],
            }).then((isConfirm) => {
                if (isConfirm) {
                    if(event.url !="")
                    {
                     location.href = event.url;
                    }
                } else {
                    swal("@lang("$string_file.ok_thanks")");
                }
            });
        @endif
        }

        OneSignalDeferred.push(function() {
          OneSignal.Notifications.addEventListener("foregroundWillDisplay", foregroundWillDisplayListener);
        });


    function pushSubscriptionChangeListener(event) {
      if (event.current.optedIn) {
        console.log(`The push subscription has received a token! ${event.current.optIn}`);
        console.log(`userid ${event.current.id}`)
        console.log(`token ${event.current.token}`)

            var status = 0;
            var player_id = event.current.id;
            if (event.current.token) {
                status = 1;
            }
            if(player_id){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: "<?php echo route('merchant-laundry-outlet-playerid.onesignal') ?>",
                    cache: false,
                    method: "POST",
                    //dataType: "json",
                    data: {
                        status:status,
                        player_id: player_id,
                        merchant_id: "{{$merchant_id}}",
                        laundry_outlet_id:"{{$laundry_outlet_id}}",
                    },
                    success: function (data) {
                        //jQuery("#showloader").hide();
                        //data-toggle="modal" data-user="'+response[f]['id']+'" data-id="'+response[f]['days']+'" data-target="#leave"
                        //window.location.href = data;
                    }
                });
            }

      }
    }

    OneSignalDeferred.push(function() {
      OneSignal.User.PushSubscription.addEventListener("change", pushSubscriptionChangeListener);
    });

    @endif
</script>
</body>
@yield('js')
</html>
