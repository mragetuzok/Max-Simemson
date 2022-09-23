$fname=$_GET['fname'];
$fsize=filesize('secret_data/'.$fname);
$fdown='secret_data/'.$fname;

// Установлена или нет переменная HTTP_RANGE
if (@getenv('HTTP_RANGE')=="") {
  // Читать и отдавать файл от самого начала
  $f=fopen($fdown, 'r');

  header("HTTP/1.1 200 OK");
  header("Connection: close");
  header("Content-Type: application/octet-stream");
  header("Accept-Ranges: bytes");
  header("Content-Disposition: Attachment; filename=".$fname);
  header("Content-Length: ".$fsize);

  while (!feof($f)) {
    if (connection_aborted()) {
      fclose($f);
      break;
    }
    echo fread($f, 10000);
    sleep(1);
  }
  fclose($f);
}
else {
  // Получить значение переменной HTTP_RANGE
  preg_match ("/bytes=(\d+)-/", getenv('HTTP_RANGE'), $m);
  $csize=$fsize-$m[1];  // Размер фрагмента
  $p1=$fsize-$csize;    // Позиция, с которой начинать чтение файла
  $p2=$fsize-1;         // Конец фрагмента

  // Установить позицию чтения в файле
  $f=fopen($fdown, 'r');
  fseek ($f, $p1);

  header("HTTP/1.1 206 Partial Content");
  header("Connection: close");
  header("Content-Type: application/octet-stream");
  header("Accept-Ranges: bytes");
  header("Content-Disposition: Attachment; filename=".$fname);
  header("Content-Range: bytes ".$p1."-".$p2."/".$fsize);
  header("Content-Length: ".$csize);

  while (!feof($f)) {
    if (connection_aborted()) {
      fclose($f);
      break;
    }
    echo fread($f, 10000);
    sleep(1);
  }
  fclose($f);
}