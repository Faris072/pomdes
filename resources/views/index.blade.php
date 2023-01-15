<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<title>Pomdes</title>
		<meta name="description" content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 94,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
		<meta name="keywords" content="Metronic, bootstrap, bootstrap 5, Angular, VueJs, React, Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta charset="utf-8" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
		<meta property="og:url" content="https://keenthemes.com/metronic" />
		<meta property="og:site_name" content="Keenthemes | Metronic" />
		<link rel="canonical" href="Https://preview.keenthemes.com/metronic8" />
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
        @include('components.css')
        @vite('resources/css/app.css')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

        <div id="app"></div>

        <script>
            let baseUrl = `{{url('/')}}`;
            let urlApi = baseUrl + '/api/';
            let assetUrl = `{{asset('/')}}`;

            function reinitializeAllPlugin() {
                $(".drawer-overlay").remove();
                setTimeout(() => {
                    KTDialer.init();
                    KTDrawer.init();
                    KTImageInput.init();
                    KTMenu.createInstances()
                    KTPasswordMeter.init();
                    KTScroll.init();
                    KTScrolltop.init();
                    KTSticky.init();
                    KTSwapper.init();
                    KTToggle.init();
                    KTUtil.onDOMContentLoaded((function () {
                        KTApp.init()
                    })), window.addEventListener("load", (function () {
                        KTApp.initPageLoader()
                    })), "undefined" != typeof module && void 0 !== module.exports && (module.exports = KTApp);

                    KTUtil.onDOMContentLoaded((function () {
                        KTLayoutAside.init()
                    }));


                    KTUtil.onDOMContentLoaded((function () {
                        KTLayoutSearch.init()
                    }));

                    KTUtil.onDOMContentLoaded((function () {
                        KTLayoutToolbar.init()
                    }));

                }, 100);

                setTimeout(() => {
                    $('body').attr('data-kt-drawer-aside', 'off');
                    $('body').attr('data-kt-drawer', 'off');
                    $('body').attr('data-kt-aside-minimize', 'off');

                    $(".drawer-overlay").remove();
                }, 10);


                $("#kt_aside_mobile_toggle").on('click', function () {
                    setTimeout(() => {


                        $('.drawer-overlay').each(function () {
                            let checkLength = $(".drawer-overlay").length;

                            if (checkLength > 1) {
                                $(this).remove();
                            }

                        });
                    }, 10);
                });

            }


            function reinitializeKTMenuPlugin() {
                KTMenu.createInstances()
            }
        </script>
        @vite('resources/js/app.js')
        @include('components.js')
	</body>
	<!--end::Body-->
</html>
