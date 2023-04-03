<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
    * {
      color: black;
    }

    .bold {
      font-weight: bold;
    }

    .normal {
      font-weight: normal;
    }

    .flex {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 200px;
      min-width: 100vw;
    }

    .important {
      color: red,
    }
  </style>
</head>
<body>
  <div>
    <p>Hey, {{ $user->username }} </p>
    <h3 class="normal">Your token for resetting password is</h3>
    <div class="flex">
      <h1>{{ $token }}</h1>
      <p class="important"><span>Note: </span>It will expire in 30 minutes</p>
    </div>
  </div>
</body>
</html>