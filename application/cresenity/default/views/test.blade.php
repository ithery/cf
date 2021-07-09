<!-- Stored in resources/views/layouts/master.blade.php -->

<head>
    <title>@CAppPageTitle</title>
    @CAppStyles
    
</head>
<body>
    <div class="container">
    @CAppContent
    <component:member-table />
    </div>
    
    
    
    
    
    @CAppScripts
    
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>
   
    @stack('scripts')
</body>
</html>