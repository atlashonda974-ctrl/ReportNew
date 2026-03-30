<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MotaAdmin - Bootstrap Admin Dashboard" />
    <meta property="og:title" content="MotaAdmin - Bootstrap Admin Dashboard" />
    <meta property="og:description" content="MotaAdmin - Bootstrap Admin Dashboard" />
    <meta property="og:image" content="social-image.png" />
    <meta name="format-detection" content="telephone=no">

    <title>Atlas Insurance - Auto Secure</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- Other CSS -->
    <link href="{{URL::asset('vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{URL::asset('vendor/chartist/css/chartist.min.css') }}">
    <link href="{{URL::asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('css/style.css') }}" rel="stylesheet">
    <link href="{{URL::asset('css/lineicon.css') }}" rel="stylesheet">

    <!-- jQuery (loaded only once) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap 4 JS Bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php
use Illuminate\Support\Facades\Session;
function hasPassed30Daysmaster() {
  $userDate = Session::get('user')['updated_at']; 
  if (is_null($userDate)) {
    return true;
  }else{
    $givenDate = new DateTime($userDate);
    $currentDate = new DateTime();
    $difference = $currentDate->diff($givenDate);
    return $difference->days >= 25 && $difference->invert == 1;
  }
}
?>

{{ View::make('header') }}

{{ View::make('navbar') }}

@yield('content')

    <!-- JavaScript Libraries -->
    <script src="{{ asset('cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js')}}"></script>
    <script src="{{ asset('js/deznav-init.js')}}"></script>
    <script src="{{asset('vendor/global/global.min.js') }}"></script>
    <script src="{{asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{asset('js/custom.min.js') }}"></script>
    <script src="{{asset('vendor/apexchart/apexchart.js') }}"></script>
    <script src="{{asset('vendor/chartist/js/chartist.min.js') }}"></script>
    <script src="{{asset('js/dashboard/dashboard-1.js') }}"></script>
    <script src="{{asset('vendor/svganimation/vivus.min.js') }}"></script>
    <script src="{{asset('vendor/svganimation/svg.animation.js') }}"></script>

    @yield('scripts')

    <script>
        function getUrlParams(dParam) {
            var dPageURL = window.location.search.substring(1),
                dURLVariables = dPageURL.split('&'),
                dParameterName,
                i;

            for (i = 0; i < dURLVariables.length; i++) {
                dParameterName = dURLVariables[i].split('=');

                if (dParameterName[0] === dParam) {
                    return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
                }
            }
        }
        
        (function($) {
            "use strict"

            var direction = getUrlParams('dir');
            if(direction != 'rtl') { direction = 'ltr'; }
            
            var dezSettingsOptions = {
                typography: "roboto",
                version: "light",
                layout: "vertical",
                headerBg: "color_1",
                navheaderBg: "color_3",
                sidebarBg: "color_1",
                sidebarStyle: "mini",
                sidebarPosition: "fixed",
                headerPosition: "fixed",
                containerLayout: "wide",
                direction: direction
            };
            
            new dezSettings(dezSettingsOptions); 
            
            jQuery(window).on('resize',function(){
                var sidebar = 'mini';
                var screenWidth = jQuery(window).width();
                if(screenWidth < 600){
                    sidebar = 'overlay';
                }
                dezSettingsOptions.sidebarStyle = sidebar;
                new dezSettings(dezSettingsOptions); 
            });

        })(jQuery);
    </script>
</body>
</html>