
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Flow Pro Studio Desk | Professional Music Production & Recording Desk | FlowState Desks</title>

    <meta name="description"
          content="Whether you're building your first home studio or upgrading a professional production room, the Flow Pro Studio Desk is designed for recording, mixing, mastering, and music production. Featuring 18U of rack space, premium cable management, and a heavy-duty sliding keyboard tray for MIDI controllers and synthesizers.">

    <meta name="keywords"
          content="studio desk, music production desk, recording studio desk, producer desk, mixing desk, mastering desk, home studio desk, studio workstation, rack mount desk, MIDI controller desk, synth workstation, audio production furniture, recording furniture, studio furniture Canada, Flow Pro Studio Desk, FlowState Desks">

    <meta name="author" content="FlowState Desks">
    <meta name="robots" content="index, follow">

    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="FlowState Desks">
    <meta property="og:title" content="Flow Pro Studio Desk | Professional Recording Studio Furniture">
    <meta property="og:description"
          content="Built for creativity with 18U of rack space, integrated cable management, and a heavy-duty keyboard tray. Designed for recording, mixing, mastering, and music production.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/flow-pro-og.jpg') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Flow Pro Studio Desk | FlowState Desks">
    <meta name="twitter:description"
          content="Professional music production desk with 18U rack space, cable management, and a 75 lb sliding keyboard tray.">
    <meta name="twitter:image" content="{{ asset('images/flow-pro-og.jpg') }}">

    <!-- Product Schema -->
    @verbatim
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
    "@type": "Product",
    "name": "Flow Pro Studio Desk",
    "brand": {
        "@type": "Brand",
        "name": "FlowState Desks"
    },
    "description": "Professional studio desk designed for recording, mixing, mastering, and music production. Features 18U rack space, cable management, and a heavy-duty sliding keyboard tray.",
    "category": "Studio Desk"
}
        </script>
    @endverbatim

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- ⭐ Only ONE Vite call -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
