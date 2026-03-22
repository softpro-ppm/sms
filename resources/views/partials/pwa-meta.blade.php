{{-- Favicon: Softpro logo on all pages --}}
<link rel="icon" href="{{ asset('images/logo/Logo_png.png') }}" type="image/png">
<link rel="shortcut icon" href="{{ asset('images/logo/Logo_png.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('images/logo/Logo_png.png') }}">
{{-- PWA: Web App Manifest & Meta --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0284c7">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="SOFTPRO SMS">
<script>
(function(){if('serviceWorker' in navigator){navigator.serviceWorker.register('{{ asset("sw.js") }}',{scope:'/'}).catch(function(){})}})();
</script>
