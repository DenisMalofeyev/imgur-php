<?php
#################################################
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // проверяем подходящий ли файл
    if(isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png","mp3" => "audio/mp3","mp4" => "audio/mp4");
        $filename = $_FILES["photo"]["name"];
        $filetype = $_FILES["photo"]["type"];
        $filesize = $_FILES["photo"]["size"];
  $uriimg='https://server.com/upload/';##ваш сайт и папка для загрузки
        // проверка разрешения файла
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");

        // максимум 50 мб. но можно увеличить до 100
        $maxsize = 50 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");

        //  MYME type of the file
        if(in_array($filetype, $allowed)){
            // проверка существования файла 
            if(file_exists("upload/" . $filename)){
                echo $filename . " is already exists.";
            } else{
                move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $filename);
                if(file_exists("upload/" . $filename)){
                  $img= "$uriimg/$filename";
                  $client_id = '';### получить можно создав приложение на имгур
                $client_secret = '';### получить можно создав приложение на имгур


                  $curl = curl_init();

                  curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.imgur.com/3/image",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => array('image' => $img),
                    CURLOPT_HTTPHEADER => array(
                      "Authorization: Client-ID $client_id"
                    ),
                  ));

                  $response = curl_exec($curl);
                  $err = curl_error($curl);

                  curl_close($curl);

                  if ($err) {
                    echo "cURL Error #:" . $err;
                  } else {
                  //  echo $response;
### декодируем ответ и превращаем в изображение в браузере
$result = json_decode($response, true);
foreach ($result as $r) {
  ?><img src="<?php  echo $r['link']; ?>" > <?php
}
### проверка существования файла и его удаление
if(file_exists("upload/" . $filename)){
    unlink ("upload/" . $filename);
}          }
                }
            }
        } else{
            echo "Error: There was a problem uploading your file. Please try again.";
        }
    } else{
        echo "Error: " . $_FILES["photo"]["error"];
    }
### 25.08.2019
