<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>OAI-PMH Explorer</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1><a href="./">OAI-PMH Explorer</a></h1>

<? 
if ($info) require __DIR__ . '/info.html.php';
if ($sets) require __DIR__ . '/sets.html.php';
if ($entry) require __DIR__ . '/entry.html.php';
if ($entries) require __DIR__ . '/entries.html.php';
if ($links) require __DIR__ . '/links.html.php'; 
?>

</body>
</html>