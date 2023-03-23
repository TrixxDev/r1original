<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <title>R1 Riepu Serviss - Pieraksta atcelšana</title>
  <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <style>
    .confirm-delete-body {
      text-align: center;
      margin-bottom: 45px;
      font-size: calc(1.5rem + 1vw);
    }
  </style>
</head>
<body>

<div class="container-fluid mt-5">
  <div class="row">
    <form method="post">
    <div class="col confirm-delete-col">
      <div class="confirm-delete-body">
        <div>
          Jūsu pieraksts:
          Ulbrokā, piektdien, 03.03.2023, pl. 17:40 <br>
          Automašīnai: Vw Golf <br>
          Vai vēlaties atcelt pierakstu?
        </div>

      </div>
      <div class="row" style="text-align: center;">
        <div class="col">
          <button class="btn btn-primary" type="submit" name="delete" style="width: 75%; font-size: 3rem;">Jā</button>
        </div>
        <div class="col">
          <button class="btn btn-secondary" type="submit" name="cancel" style="width: 75%; font-size: 3rem;">Nē</button>
        </div>
      </div>
    </div>
    </form>
  </div>
</div>



<script>
  if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    // IF MOBILE
    $('.confirm-delete-body').css({"font-size": "4.5rem"});
    $('.confirm-delete-col').css({"margin-top": "200px"});

  } else {
    // IF DESKTOP
    $('.container-fluid').addClass('container').removeClass('container-fluid');
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
