<!-- Stored in resources/views/layouts/master.blade.php -->

<head>
    <title>@CAppPageTitle</title>
    @CAppStyles

</head>
<body>
    <div class="container">
        @CAppContent
        <component:test-upload />
    </div>





    @CAppScripts

</body>
</html>