<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>OAI Explorer</title>
  <style>body { font-family: sans-serif; }</style>
</head>

<body>

	<h1>OAI Explorer</h1>

	<form>
		<label>OAI Service Provider Base URL: <input name="server" type="url" size="100"></label>
		<input type="submit" value="Show">
	</form>

	<div>or choose a service provider from the list below:</div>

	<ul>
	<? foreach ($items as $item): ?>
		<li>
			<div><a rel="service" href="./?server=<? h($item['url']) ?>"><? h($item['name'] ? $item['name'] : $item['url']) ?></a></div>
			<? if ($item['description']): ?><p><? h($item['description']); ?></p><? endif; ?>
		</li>
	<? endforeach; ?>
	</ul>

</body>
</html>