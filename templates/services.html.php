<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>OAI-PMH Explorer</title>
  <style>body { font-family: sans-serif; }</style>
</head>

<body>

	<h1>OAI-PMH Explorer</h1>

	<form>
		<label>Enter an OAI-PMH data provider base URL: <input name="server" type="url" size="100"></label>
		<input type="submit" value="Explore">
	</form>

	<div>or choose a repository from the list below:</div>

	<ul>
	<? foreach ($items as $item): ?>
		<li>
			<div><a rel="service" href="./?server=<? h($item['url']) ?>"><? h($item['name'] ? $item['name'] : $item['url']) ?></a></div>
			<? if ($item['description']): ?><p><? h($item['description']); ?></p><? endif; ?>
		</li>
	<? endforeach; ?>
	</ul>

	<div>Source: <a href="http://www.opendoar.org/">DOAR</a></div>

	<a href="https://github.com/hubgit/oai-html"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>
</body>
</html>