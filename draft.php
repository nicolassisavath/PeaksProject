<?php 

/*
thumbnail : url for an image
http://i.annihil.us/u/prod/marvel/i/mg/3/40/4bb4680432f73/portrait_xlarge.jpg


******************************* API
API to present 20 hero from the 100th
http://gateway.marvel.com/v1/public/characters?ts=1&apikey=5f9fafa4c65f4c31bc15b9301203835f&hash=c2376eedba988967c6a18a5f9bc4d41a&offset=100&limit=20

*************cURL
<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://gateway.marvel.com/v1/public/characters?ts=1&apikey=5f9fafa4c65f4c31bc15b9301203835f&hash=c2376eedba988967c6a18a5f9bc4d41a&offset=100&limit=20",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "    {\n        \"customerId\": \"AAAAA\",\n        \"companyName\": \"AAA\",\n        \"contactName\": \"AAB\",\n        \"contactTitle\": \"Azgeg\",\n        \"address\": \"AAB\"\n    }",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Postman-Token: f108ff13-be8d-467e-8596-1ff701d69046",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
 */

echo "salut php, ca faisait un bail";

echo "<br/>";


$ts="1";
$publickey = "5f9fafa4c65f4c31bc15b9301203835f";
$privateKey = "3e518ba29dc0b6b82a4875cc0a72ed2171aa00d9";

$base = "http://gateway.marvel.com/v1/public/comics?";

$toHash = $ts . $privateKey . $publickey;

$hash = md5($toHash);

echo $toHash;
echo "<br/>";

echo $hash;

$url = $base . "ts=" . $ts . "&apikey=" . $publickey . "&hash=" . $hash;

echo "<br/>";
echo $hash;
echo "<br/>";
echo $url;
echo "<br/>";


$pwd = "partie";

$hash = password_hash($pwd, PASSWORD_DEFAULT);

echo $hash; //$2y$10$BUuLKHsL8FENicIlrUoQReIG5wXihXSk0ArgwWio4ge2580OerMku


$savedHash = '$2y$10$BUuLKHsL8FENicIlrUoQReIG5wXihXSk0ArgwWio4ge2580OerMku';
echo "<br/>";
echo "pwd1";
echo "<br/>";
$pwd1 = "partie";
echo password_verify($pwd1, $savedHash);


echo "<br/>";
echo "pwd2";
echo "<br/>";
$pwd2 = "partie2";
echo password_verify($pwd2, $savedHash);
?>