<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://unpkg.com/sweetalert/dist/sweetalert.css">
    <link rel="stylesheet" href="{{asset('assets/fonts/axfonts/Alethiapro/alethiapro-regular.otf')}}">
    <style>
      @font-face {
    font-family: 'AlethiaPro';
    src: url({{asset('assets/fonts/axfonts/Alethiapro/alethiapro-regular.otf')}}) format('opentype');
    font-weight: normal;
    font-style: normal;
        }
        body {
    font-family: 'AlethiaPro', sans-serif !important;
    margin-bottom: 0px !important;
}
        </style>
</head>
<body>



    <div id="quizIndex">
        <index :quiz="{{$quiz}}" :attempt="{{$attempt}}"></index>
    </div>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{asset('vue/js/quiz.js')}}"></script>
</body>
</html>
